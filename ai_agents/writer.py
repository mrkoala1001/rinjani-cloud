import os
import sys
from crewai import Agent, Task, Crew, Process
from dotenv import load_dotenv
import urllib.request
import random
import string

# Load environment variables
load_dotenv('/var/www/hotpot/.env')

def generate_content(topic):
    # Setup LLM
    groq_key = os.getenv("GROQ_API_KEY")
    gemini_key = os.getenv("GEMINI_API_KEY")
    openai_key = os.getenv("OPENAI_API_KEY")

    if groq_key:
        # Use Groq (RECOMMENDED - Unlimited, Fast)
        os.environ["GROQ_API_KEY"] = groq_key
        llm = "groq/llama-3.3-70b-versatile"
    elif gemini_key:
        # Fallback to Gemini
        os.environ["GOOGLE_API_KEY"] = gemini_key
        if "GEMINI_API_KEY" in os.environ:
            del os.environ["GEMINI_API_KEY"]
        llm = "gemini/gemini-1.5-flash"
    elif openai_key:
        # Fallback to OpenAI
        llm = "openai/gpt-4o"
    else:
        print("ERROR: No API Key found in .env (GROQ_API_KEY, GEMINI_API_KEY, or OPENAI_API_KEY)")
        sys.exit(1)

    # 1. Researcher Agent
    researcher = Agent(
        role='Analis Riset Senior',
        goal=f'Mencari tren terbaru dan fakta kunci tentang {topic}',
        backstory="""Anda adalah ahli dalam menemukan informasi relevan, statistik, dan 
        perkembangan terbaru. Anda menyajikan ringkasan yang ringkas dan faktual dalam Bahasa Indonesia.""",
        llm=llm,
        allow_delegation=False
    )

    # 2. Writer Agent
    writer = Agent(
        role='Penulis Artikel Teknologi Profesional dan Ahli SEO',
        goal=f'Menulis artikel blog yang mendalam, menarik, dan teroptimasi SEO tentang {topic} yang berfokus pada dunia Teknologi dan Website dalam Bahasa Indonesia',
        backstory="""Anda adalah seorang penulis senior dengan spesialisasi mendalam di bidang Teknologi Informasi, 
        Web Development, dan Digital Infrastructure. Selama lebih dari 10 tahun, Anda telah berkarya untuk publikasi teknologi 
        terkemuka. Keahlian utama Anda adalah mengubah topik teknis yang rumit menjadi tulisan yang mengalir indah, 
        mudah dipahami, dan sangat ramah mesin pencari (SEO). Anda sangat mengerti penggunaan keyword, 
        heading hierarchy, dan readability dalam konteks industri teknologi.""",
        llm=llm,
        allow_delegation=False
    )

    # 3. Editor Agent
    editor = Agent(
        role='Chief Content Officer & Pakar SEO Teknologi',
        goal='Tinjau dan sempurnakan artikel teknologi untuk kualitas tulisan kelas dunia dan skor SEO maksimal',
        backstory="""Sebagai pemimpin redaksi di media teknologi besar, Anda memastikan setiap konten teknis akurat dan bermakna. 
        Anda membuang filler, memperbaiki struktur agar optimal untuk index Google di niche teknologi dan website, 
        dan memastikan tone tulisan tetap manusiawi serta profesional dalam Bahasa Indonesia. 
        Pastikan hasil akhir bersih dari formatting aneh dan benar-benar relevan untuk audiens pengembang/pengguna teknologi.""",
        llm=llm,
        allow_delegation=False
    )

    # 4. Visual Designer Agent
    visual_designer = Agent(
        role='Visual Designer & AI Prompt Engineer',
        goal='Menciptakan prompt visual yang menakjubkan untuk ilustrasi blog',
        backstory="""Anda ahli dalam menerjemahkan konten teks menjadi visi artistik. 
        Anda mengerti cara membuat prompt untuk AI Image Generator (seperti DALL-E atau Midjourney) 
        untuk menghasilkan gambar 3D render, maskot teknologi, atau ilustrasi modern yang premium.""",
        llm=llm,
        allow_delegation=False
    )

    # Define Tasks
    task_research = Task(
        description=f"Analyze the topic: {topic}. Identify 3-5 key points and current trends.",
        expected_output="A summary of key facts and trends.",
        agent=researcher
    )

    task_write = Task(
        description=f"Using the research, write a blog post about {topic} in Bahasa Indonesia. Use Markdown formatting. Include headings and bullet points.",
        expected_output="A full blog post in Indonesian language (Markdown).",
        agent=writer
    )

    task_edit = Task(
        description="Review the blog post for quality and SEO. Ensure it's engaging and professional in Indonesian.",
        expected_output="The final polished blog post in Markdown format.",
        agent=editor
    )

    task_visual = Task(
        description="Berdasarkan ringkasan artikel, buatlah satu prompt gambar dalam Bahasa Inggris. "
                    "Prompt harus mendeskripsikan ilustrasi 3D render teknologi yang modern, premium, dan vibrant. "
                    "Hanya berikan teks prompt-nya saja tanpa penjelasan tambahan.",
        expected_output="A single English prompt for an AI image generator.",
        agent=visual_designer
    )

    # Create Crew
    # Disable telemetry to reduce background threads
    crew = Crew(
        agents=[researcher, writer, editor, visual_designer],
        tasks=[task_research, task_write, task_edit, task_visual],
        process=Process.sequential,
        verbose=False,
        share_crew=False # Disables telemetry
    )

    # Execute
    result = crew.kickoff()
    
    # Extract results. CrewAI kickoff returns a CrewOutput object.
    # We want the output of task_edit (article) and task_visual (prompt)
    # The result object can be indexed or we can access tasks directly if needed.
    # For simplicity, we'll assume the final result contains the last task output
    # but CrewAI allows access to individual task outputs.
    
    article = task_edit.output.raw
    image_prompt = task_visual.output.raw
    
    # Generate actual image from Pollinations.ai
    image_url = generate_image(image_prompt, topic)
    
    return article, image_prompt, image_url

def generate_image(prompt, topic):
    """
    Generate and download image from Pollinations.ai
    Returns the image URL
    """
    try:
        # Clean the prompt
        cleaned_prompt = prompt.strip().strip('"\'')
        
        # URL encode the prompt
        from urllib.parse import quote
        encoded_prompt = quote(cleaned_prompt)
        
        # Generate random seed for variety
        seed = random.randint(1, 1000000)
        
        # Pollinations.ai URL
        image_url = f"https://image.pollinations.ai/prompt/{encoded_prompt}?width=1280&height=720&nologo=true&seed={seed}"
        
        # Create directory if not exists
        import os
        os.makedirs('/tmp/ai_images', exist_ok=True)
        
        # Generate filename
        rand_str = ''.join(random.choices(string.ascii_lowercase + string.digits, k=8))
        filename = f"ai_{rand_str}_{topic.replace(' ', '_')[:30]}.png"
        filepath = f"/tmp/ai_images/{filename}"
        
        # Try to download the image
        try:
            urllib.request.urlretrieve(image_url, filepath)
            print(f"✅ Image generated: {image_url}")
            return image_url
        except Exception as e:
            print(f"⚠️ Failed to download image: {e}")
            return image_url  # Return URL anyway, even if download failed
            
    except Exception as e:
        print(f"❌ Error generating image: {e}")
        return None

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python3 writer.py 'Topic'")
        sys.exit(1)
    
    topic = sys.argv[1]
    try:
        article, image_prompt, image_url = generate_content(topic)
        print("---CONTENT_START---")
        print(article)
        print("---CONTENT_END---")
        print("---IMAGE_PROMPT_START---")
        print(image_prompt)
        print("---IMAGE_PROMPT_END---")
        print("---IMAGE_URL_START---")
        print(image_url if image_url else "")
        print("---IMAGE_URL_END---")
        # Ensure clean exit by flushing buffers and using os._exit
        # This prevents the interpreter from crashing on lingering background threads
        sys.stdout.flush()
        sys.stderr.flush()
        os._exit(0)
    except Exception as e:
        print(f"ERROR: {e}")
        sys.stdout.flush()
        os._exit(1)
