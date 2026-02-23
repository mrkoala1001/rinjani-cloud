#!/usr/bin/env python3
"""
Simple Social Media Caption Generator
Digunakan saat LLM rate limit atau tidak tersedia
"""
import json
import random

def generate_simple_captions(title, content):
    """
    Generate captions menggunakan template simple
    Tidak perlu LLM API
    """
    
    # Extract key words dari title
    words = title.split()[:3]  # Ambil 3 kata pertama
    
    # Caption templates
    instagram_story = f"🚀 {title} 🚀"
    
    instagram_feed = f"📝 {title}\n\nBaca artikel lengkap kami untuk info lebih detail!\n\n#teknologi #blog #artikel"
    
    facebook_post = f"Halo semua! 👋\n\nKami baru publish artikel tentang: {title}\n\nTopik ini sangat penting untuk kalian yang tertarik dengan dunia teknologi dan bisnis digital.\n\nYuk baca selengkapnya dan share pendapat kalian!\n\n#teknologi #artikel #bisnis"
    
    hashtags = [
        "#teknologi",
        "#artikel", 
        "#blog",
        "#bisnis",
        "#digital",
        "#informasi",
        "#edukasi",
        "#tips",
        "#trending",
        "#update"
    ]
    
    cta = "Baca Selengkapnya"
    
    emojis = ["🚀", "📝", "💡", "🎯", "✨", "💪", "🔥", "📱"]
    
    return {
        "instagram_story": instagram_story,
        "instagram_feed": instagram_feed,
        "facebook_post": facebook_post,
        "hashtags": hashtags,
        "cta": cta,
        "emojis": emojis
    }

if __name__ == "__main__":
    import sys
    
    if len(sys.argv) < 2:
        print("Usage: python3 simple_caption_gen.py 'Title'")
        sys.exit(1)
    
    title = sys.argv[1]
    content = sys.argv[2] if len(sys.argv) > 2 else ""
    
    captions = generate_simple_captions(title, content)
    print(json.dumps(captions, indent=2))
