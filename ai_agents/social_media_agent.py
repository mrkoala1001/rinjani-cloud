#!/usr/bin/env python3
import os
import sys
from crewai import Agent, Task, Crew, Process
from dotenv import load_dotenv
import json

# Load environment variables
load_dotenv('/var/www/hotpot/.env')

def generate_social_content(article_content, article_title, image_url):
    """
    Generate social media content for Facebook & Instagram
    """
    
    # Setup LLM with fallback
    groq_key = os.getenv("GROQ_API_KEY")
    gemini_key = os.getenv("GEMINI_API_KEY")
    openai_key = os.getenv("OPENAI_API_KEY")
    
    # Try Groq first, then Gemini, then OpenAI
    if groq_key:
        try:
            os.environ["GROQ_API_KEY"] = groq_key
            llm = "groq/llama-3.3-70b-versatile"
        except Exception as e:
            print(f"⚠️ Groq error: {e}")
            # Fallback to Gemini
            if gemini_key:
                os.environ["GOOGLE_API_KEY"] = gemini_key
                llm = "gemini/gemini-1.5-flash"
            elif openai_key:
                llm = "openai/gpt-4o"
            else:
                print("ERROR: No API Key available")
                sys.exit(1)
    elif gemini_key:
        os.environ["GOOGLE_API_KEY"] = gemini_key
        llm = "gemini/gemini-1.5-flash"
    elif openai_key:
        llm = "openai/gpt-4o"
    else:
        print("ERROR: No API Key found (GROQ_API_KEY, GEMINI_API_KEY, or OPENAI_API_KEY)")
        sys.exit(1)
    
    
    # Social Media Content Agent
    social_agent = Agent(
        role='Social Media Content Specialist & Copywriter',
        goal='Create engaging social media content untuk Facebook dan Instagram yang drive engagement dan clicks',
        backstory="""Anda adalah expert social media marketer dengan pengalaman membuat viral content. 
        Anda tahu algoritma FB dan IG, understand audience behavior, dan bisa write compelling copy yang drive action. 
        Anda expert dalam hashtags, emojis, dan CTA yang convert.""",
        llm=llm,
        allow_delegation=False
    )
    
    # Image Specialist Agent
    image_agent = Agent(
        role='Visual Content Optimizer',
        goal='Optimize image dimensions dan create image descriptions untuk setiap platform',
        backstory="""Anda adalah visual content expert yang paham spesifikasi teknis setiap platform media sosial. 
        Anda tahu optimal image sizes, color psychology, dan design principles.""",
        llm=llm,
        allow_delegation=False
    )
    
    # Tasks
    task_social = Task(
        description=f"""Buat social media content untuk artikel ini:
        Judul: {article_title}
        
        Untuk setiap platform (Facebook & Instagram), buat:
        1. Short version (140 chars) - untuk Instagram Stories caption
        2. Medium version (250 chars) - untuk Feed posts
        3. Long version (500 chars) - untuk Facebook detailed post
        4. Hashtags: 10-15 relevant hashtags, optimal untuk reach
        5. CTA: Call-to-action yang jelas (Read More, Learn More, dsb)
        6. Emoji suggestions untuk visual engagement
        
        Article excerpt: {article_content[:300]}...
        
        Format output sebagai JSON dengan keys: instagram_story, instagram_feed, facebook_post, hashtags, cta, emojis""",
        expected_output="JSON content dengan berbagai variations untuk FB dan IG",
        agent=social_agent
    )
    
    task_image = Task(
        description=f"""Buat image optimization untuk social media:
        
        Original image URL: {image_url}
        
        Generate specifications untuk:
        1. Facebook Feed: 1200x628 pixels, aspect ratio 1.91:1
        2. Instagram Feed: 1080x1080 pixels (square), aspect ratio 1:1
        3. Instagram Stories: 1080x1920 pixels (vertical), aspect ratio 9:16
        4. Image descriptions dan alt-text
        
        Format output sebagai JSON dengan keys: facebook_specs, instagram_feed_specs, instagram_stories_specs, alt_text""",
        expected_output="JSON dengan image specifications untuk setiap platform",
        agent=image_agent
    )
    
    # Create Crew
    crew = Crew(
        agents=[social_agent, image_agent],
        tasks=[task_social, task_image],
        process=Process.sequential,
        verbose=False,
        share_crew=False
    )
    
    # Execute
    result = crew.kickoff()
    
    # Extract results
    social_content = task_social.output.raw
    image_specs = task_image.output.raw
    
    return social_content, image_specs

if __name__ == "__main__":
    if len(sys.argv) < 4:
        print("Usage: python3 social_media_agent.py 'Article Title' 'Article Content' 'Image URL'")
        sys.exit(1)
    
    title = sys.argv[1]
    content = sys.argv[2]
    image_url = sys.argv[3]
    
    try:
        social_content, image_specs = generate_social_content(content, title, image_url)
        
        print("---SOCIAL_CONTENT_START---")
        print(social_content)
        print("---SOCIAL_CONTENT_END---")
        print("---IMAGE_SPECS_START---")
        print(image_specs)
        print("---IMAGE_SPECS_END---")
        
        sys.stdout.flush()
        sys.stderr.flush()
        os._exit(0)
    except Exception as e:
        print(f"ERROR: {e}")
        sys.stdout.flush()
        os._exit(1)
