#!/usr/bin/env python3
"""
WordPress Customization Script for Loja Dropshipping
Uses WordPress REST API to implement customizations based on research analysis
"""

import requests
import json
import sys
import time
import base64

# WordPress site details
WP_URL = "https://wordpress-tc48ws4ccowwwc8s0sockooo.m2w.io"
WP_API = f"{WP_URL}/wp-json"
USERNAME = "silvio@m2w.digital"
PASSWORD = "Malasf@1980@M2w"  # Replace with actual password if needed

# Logo URL - Replace with your actual logo URL
LOGO_URL = "https://i.imgur.com/8jIGpEX.png"  # Default placeholder logo

# Color scheme based on research recommendations
COLOR_SCHEME = {
    # Primary color palette (Rosa Suave e Menta Fresca)
    "primary_color": "#E8D2D2",  # Rosa Pastel Queimado
    "secondary_color": "#D4E2D4",  # Verde Menta Pastel
    "accent_color": "#F7CAC9",  # Rosa Bebê
    "text_color": "#555555",  # Cinza Escuro
    "background_color": "#FFFFFF",  # Branco
    
    # Button colors
    "button_background_color": "#F7CAC9",  # Rosa Bebê
    "button_text_color": "#FFFFFF",  # Branco
    "button_hover_background_color": "#E8D2D2",  # Rosa Pastel Queimado
    "button_hover_text_color": "#FFFFFF",  # Branco
    
    # Header colors
    "header_background_color": "#FFFFFF",  # Branco
    "header_text_color": "#555555",  # Cinza Escuro
    
    # Footer colors
    "footer_background_color": "#F9F9F9",  # Cinza muito claro
    "footer_text_color": "#555555",  # Cinza Escuro
}

# Typography settings - Modern, sans-serif fonts
TYPOGRAPHY = {
    "body_font_family": "Open Sans",
    "heading_font_family": "Montserrat",
    "menu_font_family": "Montserrat",
    "body_font_size": "16px",
    "h1_font_size": "36px",
    "h2_font_size": "30px",
    "h3_font_size": "24px",
    "h4_font_size": "20px",
    "h5_font_size": "18px",
    "h6_font_size": "16px",
}

# Custom CSS for clean, minimal design
CUSTOM_CSS = """
/* Clean, minimal design with pastel colors */
body {
    font-family: "Open Sans", sans-serif;
    color: #555555;
    background-color: #FFFFFF;
    line-height: 1.6;
}

h1, h2, h3, h4, h5, h6 {
    font-family: "Montserrat", sans-serif;
    font-weight: 600;
    color: #333333;
}

/* Header styling */
.site-header {
    padding: 20px 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

/* Button styling */
.button, button, input[type="button"], input[type="submit"] {
    background-color: #F7CAC9;
    color: #FFFFFF;
    border-radius: 4px;
    padding: 12px 24px;
    font-weight: 600;
    border: none;
    transition: all 0.3s ease;
}

.button:hover, button:hover, input[type="button"]:hover, input[type="submit"]:hover {
    background-color: #E8D2D2;
}

/* Product styling */
.products .product {
    text-align: center;
    transition: all 0.3s ease;
}

.products .product:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

/* Clean product title */
.woocommerce-loop-product__title {
    font-size: 16px !important;
    font-weight: 500;
    margin-top: 10px !important;
}

/* Price styling */
.price {
    color: #555555 !important;
    font-weight: 600;
}

/* Footer styling */
.site-footer {
    background-color: #F9F9F9;
    padding: 40px 0;
    margin-top: 60px;
}

/* Hero section styling */
.hero-section {
    padding: 80px 0;
    text-align: center;
    background-color: #F9F9F9;
}

.hero-section h1 {
    font-size: 42px;
    margin-bottom: 20px;
}

.hero-section p {
    font-size: 18px;
    max-width: 600px;
    margin: 0 auto 30px;
}

/* Featured products section */
.featured-products {
    padding: 60px 0;
}

.section-title {
    text-align: center;
    margin-bottom: 40px;
    font-size: 28px;
    position: relative;
}

.section-title:after {
    content: "";
    display: block;
    width: 60px;
    height: 3px;
    background-color: #F7CAC9;
    margin: 15px auto 0;
}

/* About section */
.about-section {
    padding: 60px 0;
    background-color: #F9F9F9;
}

/* Testimonials */
.testimonials {
    padding: 60px 0;
}

.testimonial-item {
    text-align: center;
    padding: 20px;
    margin-bottom: 30px;
}

.testimonial-content {
    font-style: italic;
    margin-bottom: 15px;
}

.testimonial-author {
    font-weight: 600;
}

/* Newsletter section */
.newsletter-section {
    padding: 60px 0;
    background-color: #F9F9F9;
    text-align: center;
}

.newsletter-form {
    max-width: 500px;
    margin: 0 auto;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .hero-section {
        padding: 60px 0;
    }
    
    .hero-section h1 {
        font-size: 32px;
    }
    
    .section-title {
        font-size: 24px;
    }
}
"""

# Homepage content based on research recommendations
HOMEPAGE_CONTENT = """
<!-- wp:group {"className":"hero-section"} -->
<div class="wp-block-group hero-section">
    <!-- wp:heading {"level":1,"align":"center"} -->
    <h1 class="has-text-align-center">Beleza e Cuidados Essenciais</h1>
    <!-- /wp:heading -->
    
    <!-- wp:paragraph {"align":"center"} -->
    <p class="has-text-align-center">Descubra produtos de qualidade para realçar sua beleza natural</p>
    <!-- /wp:paragraph -->
    
    <!-- wp:buttons {"align":"center"} -->
    <div class="wp-block-buttons aligncenter">
        <!-- wp:button -->
        <div class="wp-block-button"><a class="wp-block-button__link">Comprar Agora</a></div>
        <!-- /wp:button -->
    </div>
    <!-- /wp:buttons -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"featured-products"} -->
<div class="wp-block-group featured-products">
    <!-- wp:heading {"className":"section-title","align":"center"} -->
    <h2 class="has-text-align-center section-title">Produtos em Destaque</h2>
    <!-- /wp:heading -->
    
    <!-- wp:woocommerce/product-best-sellers {"columns":4} -->
    <div class="wp-block-woocommerce-product-best-sellers">[products best_selling per_page="4" columns="4"]</div>
    <!-- /wp:woocommerce/product-best-sellers -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"about-section"} -->
<div class="wp-block-group about-section">
    <!-- wp:columns -->
    <div class="wp-block-columns">
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:heading {"className":"section-title"} -->
            <h2 class="section-title">Nossa Marca</h2>
            <!-- /wp:heading -->
            
            <!-- wp:paragraph -->
            <p>Somos uma marca dedicada a oferecer produtos de beleza de alta qualidade que realçam sua beleza natural. Nossos produtos são cuidadosamente selecionados para garantir eficácia e resultados visíveis.</p>
            <!-- /wp:paragraph -->
            
            <!-- wp:paragraph -->
            <p>Acreditamos que a verdadeira beleza vem de dentro, e nossos produtos são projetados para ajudar você a expressar sua beleza única.</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:image -->
            <figure class="wp-block-image"><img src="https://via.placeholder.com/600x400" alt="Sobre nossa marca"/></figure>
            <!-- /wp:image -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"testimonials"} -->
<div class="wp-block-group testimonials">
    <!-- wp:heading {"className":"section-title","align":"center"} -->
    <h2 class="has-text-align-center section-title">O Que Nossos Clientes Dizem</h2>
    <!-- /wp:heading -->
    
    <!-- wp:columns -->
    <div class="wp-block-columns">
        <!-- wp:column {"className":"testimonial-item"} -->
        <div class="wp-block-column testimonial-item">
            <!-- wp:paragraph {"className":"testimonial-content"} -->
            <p class="testimonial-content">"Os produtos são incríveis! Minha pele nunca esteve tão radiante. Recomendo totalmente!"</p>
            <!-- /wp:paragraph -->
            
            <!-- wp:paragraph {"className":"testimonial-author"} -->
            <p class="testimonial-author">— Maria S.</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        
        <!-- wp:column {"className":"testimonial-item"} -->
        <div class="wp-block-column testimonial-item">
            <!-- wp:paragraph {"className":"testimonial-content"} -->
            <p class="testimonial-content">"Entrega rápida e produtos de alta qualidade. Estou muito satisfeita com minha compra!"</p>
            <!-- /wp:paragraph -->
            
            <!-- wp:paragraph {"className":"testimonial-author"} -->
            <p class="testimonial-author">— Ana P.</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        
        <!-- wp:column {"className":"testimonial-item"} -->
        <div class="wp-block-column testimonial-item">
            <!-- wp:paragraph {"className":"testimonial-content"} -->
            <p class="testimonial-content">"Produtos que realmente cumprem o que prometem. Já fiz várias compras e sempre fico satisfeita."</p>
            <!-- /wp:paragraph -->
            
            <!-- wp:paragraph {"className":"testimonial-author"} -->
            <p class="testimonial-author">— Carla M.</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"newsletter-section"} -->
<div class="wp-block-group newsletter-section">
    <!-- wp:heading {"className":"section-title","align":"center"} -->
    <h2 class="has-text-align-center section-title">Receba Nossas Novidades</h2>
    <!-- /wp:heading -->
    
    <!-- wp:paragraph {"align":"center"} -->
    <p class="has-text-align-center">Inscreva-se para receber ofertas exclusivas e dicas de beleza</p>
    <!-- /wp:paragraph -->
    
    <!-- wp:html -->
    <div class="newsletter-form">
        <form>
            <input type="email" placeholder="Seu e-mail" style="width: 70%; padding: 12px; border: 1px solid #ddd; border-radius: 4px 0 0 4px;">
            <button type="submit" style="padding: 12px 20px; background-color: #F7CAC9; color: white; border: none; border-radius: 0 4px 4px 0; cursor: pointer;">Inscrever</button>
        </form>
    </div>
    <!-- /wp:html -->
</div>
<!-- /wp:group -->
"""

def get_auth_token():
    """Get authentication token from WordPress REST API"""
    print("Authenticating with WordPress...")
    
    # Try JWT authentication if available
    try:
        response = requests.post(f"{WP_API}/jwt-auth/v1/token", data={
            "username": USERNAME,
            "password": PASSWORD
        })
        response.raise_for_status()
        token = response.json().get("token")
        if token:
            print("JWT Authentication successful")
            return {"Authorization": f"Bearer {token}"}
    except Exception as e:
        print(f"JWT Authentication not available: {e}")
    
    # Fall back to basic authentication
    credentials = f"{USERNAME}:{PASSWORD}"
    token = base64.b64encode(credentials.encode()).decode("utf-8")
    print("Using Basic Authentication")
    return {"Authorization": f"Basic {token}"}

def update_site_identity(headers):
    """Update site title and tagline"""
    print("\nUpdating site identity...")
    
    # Update site title and tagline
    response = requests.post(
        f"{WP_API}/wp/v2/settings",
        headers=headers,
        json={
            "title": "Loja Dropshipping",
            "description": "Beleza e Cuidados Essenciais"
        }
    )
    
    if response.status_code == 200:
        print("✓ Site identity updated successfully")
    else:
        print(f"✗ Failed to update site identity: {response.status_code}")
        print(response.text)

def update_theme_mods(headers):
    """Update theme modifications (colors, typography, etc.)"""
    print("\nUpdating theme settings...")
    
    # Combine color scheme and typography settings
    theme_mods = {**COLOR_SCHEME, **TYPOGRAPHY}
    
    # Update each theme mod using the REST API
    for key, value in theme_mods.items():
        response = requests.post(
            f"{WP_API}/biagiotti/v1/theme_mod",  # Adjust endpoint based on theme
            headers=headers,
            json={
                "option_name": key,
                "option_value": value
            }
        )
        
        if response.status_code in [200, 201]:
            print(f"✓ Updated {key}: {value}")
        else:
            print(f"✗ Failed to update {key}: {response.status_code}")
    
    print("Theme settings update completed")

def update_custom_css(headers):
    """Add custom CSS to the theme"""
    print("\nUpdating custom CSS...")
    
    # First check if a custom CSS post already exists
    response = requests.get(
        f"{WP_API}/wp/v2/custom_css",
        headers=headers
    )
    
    if response.status_code == 200:
        custom_css_posts = response.json()
        if custom_css_posts:
            # Update existing custom CSS
            css_post_id = custom_css_posts[0]["id"]
            response = requests.post(
                f"{WP_API}/wp/v2/custom_css/{css_post_id}",
                headers=headers,
                json={
                    "content": CUSTOM_CSS
                }
            )
        else:
            # Create new custom CSS
            response = requests.post(
                f"{WP_API}/wp/v2/custom_css",
                headers=headers,
                json={
                    "content": CUSTOM_CSS
                }
            )
    
        if response.status_code in [200, 201]:
            print("✓ Custom CSS updated successfully")
        else:
            print(f"✗ Failed to update custom CSS: {response.status_code}")
            print(response.text)
    else:
        print(f"✗ Failed to check custom CSS: {response.status_code}")
        print(response.text)

def setup_homepage(headers):
    """Create or update homepage and set as static front page"""
    print("\nSetting up homepage...")
    
    # Check if homepage exists
    response = requests.get(
        f"{WP_API}/wp/v2/pages?slug=home",
        headers=headers
    )
    
    if response.status_code == 200:
        pages = response.json()
        if pages:
            # Update existing homepage
            homepage_id = pages[0]["id"]
            response = requests.post(
                f"{WP_API}/wp/v2/pages/{homepage_id}",
                headers=headers,
                json={
                    "title": "Home",
                    "content": HOMEPAGE_CONTENT,
                    "status": "publish"
                }
            )
            print(f"✓ Updated existing homepage (ID: {homepage_id})")
        else:
            # Create new homepage
            response = requests.post(
                f"{WP_API}/wp/v2/pages",
                headers=headers,
                json={
                    "title": "Home",
                    "content": HOMEPAGE_CONTENT,
                    "status": "publish"
                }
            )
            if response.status_code == 201:
                homepage_id = response.json()["id"]
                print(f"✓ Created new homepage (ID: {homepage_id})")
            else:
                print(f"✗ Failed to create homepage: {response.status_code}")
                print(response.text)
                return
    else:
        print(f"✗ Failed to check for existing homepage: {response.status_code}")
        print(response.text)
        return
    
    # Set as static front page
    response = requests.post(
        f"{WP_API}/wp/v2/settings",
        headers=headers,
        json={
            "show_on_front": "page",
            "page_on_front": homepage_id
        }
    )
    
    if response.status_code == 200:
        print("✓ Homepage set as static front page")
    else:
        print(f"✗ Failed to set homepage as front page: {response.status_code}")
        print(response.text)

def setup_blog_page(headers):
    """Create or update blog page and set as posts page"""
    print("\nSetting up blog page...")
    
    # Check if blog page exists
    response = requests.get(
        f"{WP_API}/wp/v2/pages?slug=blog",
        headers=headers
    )
    
    if response.status_code == 200:
        pages = response.json()
        if pages:
            # Update existing blog page
            blog_id = pages[0]["id"]
            response = requests.post(
                f"{WP_API}/wp/v2/pages/{blog_id}",
                headers=headers,
                json={
                    "title": "Blog",
                    "status": "publish"
                }
            )
            print(f"✓ Updated existing blog page (ID: {blog_id})")
        else:
            # Create new blog page
            response = requests.post(
                f"{WP_API}/wp/v2/pages",
                headers=headers,
                json={
                    "title": "Blog",
                    "status": "publish"
                }
            )
            if response.status_code == 201:
                blog_id = response.json()["id"]
                print(f"✓ Created new blog page (ID: {blog_id})")
            else:
                print(f"✗ Failed to create blog page: {response.status_code}")
                print(response.text)
                return
    else:
        print(f"✗ Failed to check for existing blog page: {response.status_code}")
        print(response.text)
        return
    
    # Set as posts page
    response = requests.post(
        f"{WP_API}/wp/v2/settings",
        headers=headers,
        json={
            "page_for_posts": blog_id
        }
    )
    
    if response.status_code == 200:
        print("✓ Blog page set as posts page")
    else:
        print(f"✗ Failed to set blog page as posts page: {response.status_code}")
        print(response.text)

def upload_logo(headers):
    """Upload and set the site logo"""
    print("\nUploading and setting site logo...")
    
    # First, download the logo image
    try:
        print(f"Downloading logo from {LOGO_URL}...")
        logo_response = requests.get(LOGO_URL)
        logo_response.raise_for_status()
        logo_data = logo_response.content
        
        # Prepare the file for upload
        files = {
            'file': ('logo.png', logo_data, 'image/png')
        }
        
        # Upload the logo to WordPress media library
        upload_response = requests.post(
            f"{WP_API}/wp/v2/media",
            headers={key: headers[key] for key in headers if key != 'Content-Type'},
            files=files
        )
        
        if upload_response.status_code in [200, 201]:
            logo_id = upload_response.json()["id"]
            logo_url = upload_response.json()["source_url"]
            print(f"✓ Logo uploaded successfully (ID: {logo_id})")
            
            # Set the logo as the site icon
            response = requests.post(
                f"{WP_API}/wp/v2/settings",
                headers=headers,
                json={
                    "site_logo": logo_id
                }
            )
            
            if response.status_code == 200:
                print("✓ Logo set as site logo")
            else:
                print(f"✗ Failed to set logo: {response.status_code}")
                print(response.text)
        else:
            print(f"✗ Failed to upload logo: {upload_response.status_code}")
            print(upload_response.text)
    except Exception as e:
        print(f"✗ Error uploading logo: {e}")

def setup_ai_features(headers):
    """Set up AI features for the store"""
    print("\nSetting up AI features...")
    
    # Add AI product recommendation widget to homepage
    ai_widget_content = """
    <!-- wp:html -->
    <div class="ai-product-recommendations">
        <h3>Recomendações Personalizadas</h3>
        <div class="ai-recommendation-container">
            <script>
                // AI Product Recommendation Script
                document.addEventListener('DOMContentLoaded', function() {
                    // This would be replaced with actual AI recommendation logic
                    console.log('AI product recommendation system initialized');
                    
                    // Simulate AI recommendations with placeholder
                    const recommendationContainer = document.querySelector('.ai-recommendation-container');
                    
                    // In a real implementation, this would call an API to get personalized recommendations
                    const products = [
                        {name: 'Sérum Facial', price: 'R$ 89,90', image: 'https://via.placeholder.com/150'},
                        {name: 'Creme Hidratante', price: 'R$ 69,90', image: 'https://via.placeholder.com/150'},
                        {name: 'Máscara Facial', price: 'R$ 49,90', image: 'https://via.placeholder.com/150'}
                    ];
                    
                    let html = '<div class="ai-products">';
                    products.forEach(product => {
                        html += `
                            <div class="ai-product">
                                <img src="${product.image}" alt="${product.name}">
                                <h4>${product.name}</h4>
                                <p class="price">${product.price}</p>
                                <button>Adicionar ao Carrinho</button>
                            </div>
                        `;
                    });
                    html += '</div>';
                    
                    recommendationContainer.innerHTML = html;
                });
            </script>
            <style>
                .ai-product-recommendations {
                    margin: 40px 0;
                    padding: 20px;
                    background-color: #f9f9f9;
                    border-radius: 8px;
                }
                .ai-product-recommendations h3 {
                    text-align: center;
                    margin-bottom: 20px;
                    color: #333;
                }
                .ai-products {
                    display: flex;
                    justify-content: space-around;
                    flex-wrap: wrap;
                }
                .ai-product {
                    width: 200px;
                    padding: 15px;
                    margin: 10px;
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                    text-align: center;
                }
                .ai-product img {
                    max-width: 100%;
                    height: auto;
                    border-radius: 4px;
                }
                .ai-product h4 {
                    margin: 10px 0;
                    font-size: 16px;
                }
                .ai-product .price {
                    color: #E8D2D2;
                    font-weight: bold;
                }
                .ai-product button {
                    background-color: #F7CAC9;
                    color: white;
                    border: none;
                    padding: 8px 15px;
                    border-radius: 4px;
                    cursor: pointer;
                    margin-top: 10px;
                    transition: background-color 0.3s;
                }
                .ai-product button:hover {
                    background-color: #E8D2D2;
                }
            </style>
        </div>
    </div>
    <!-- /wp:html -->
    
    <!-- wp:html -->
    <div class="ai-chatbot">
        <div class="chatbot-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#F7CAC9">
                <path d="M12 1c-6.627 0-12 4.364-12 9.749 0 3.131 1.817 5.917 4.64 7.7.868 2.167-1.083 4.008-3.142 4.503 2.271.195 6.311-.121 9.374-2.498 7.095.538 13.128-3.997 13.128-9.705 0-5.385-5.373-9.749-12-9.749z"/>
            </svg>
        </div>
        <div class="chatbot-popup" style="display: none;">
            <div class="chatbot-header">
                <h4>Assistente Virtual</h4>
                <span class="close-chat">×</span>
            </div>
            <div class="chatbot-messages">
                <div class="message bot">Olá! Como posso ajudar você hoje?</div>
            </div>
            <div class="chatbot-input">
                <input type="text" placeholder="Digite sua pergunta...">
                <button>Enviar</button>
            </div>
        </div>
        <script>
            // Simple chatbot functionality
            document.addEventListener('DOMContentLoaded', function() {
                const chatbotIcon = document.querySelector('.chatbot-icon');
                const chatbotPopup = document.querySelector('.chatbot-popup');
                const closeChat = document.querySelector('.close-chat');
                const chatInput = document.querySelector('.chatbot-input input');
                const chatButton = document.querySelector('.chatbot-input button');
                const chatMessages = document.querySelector('.chatbot-messages');
                
                // Toggle chatbot popup
                chatbotIcon.addEventListener('click', function() {
                    chatbotPopup.style.display = 'block';
                });
                
                closeChat.addEventListener('click', function() {
                    chatbotPopup.style.display = 'none';
                });
                
                // Handle sending messages
                function sendMessage() {
                    const message = chatInput.value.trim();
                    if (message) {
                        // Add user message
                        chatMessages.innerHTML += `<div class="message user">${message}</div>`;
                        chatInput.value = '';
                        
                        // Simulate AI response (in a real implementation, this would call an API)
                        setTimeout(() => {
                            const responses = [
                                "Obrigado por sua pergunta! Estamos processando sua solicitação.",
                                "Temos vários produtos que podem atender sua necessidade. Posso recomendar nosso sérum facial.",
                                "Nossos produtos são formulados com ingredientes naturais e de alta qualidade.",
                                "Entrega para todo o Brasil em até 7 dias úteis."
                            ];
                            const randomResponse = responses[Math.floor(Math.random() * responses.length)];
                            chatMessages.innerHTML += `<div class="message bot">${randomResponse}</div>`;
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        }, 1000);
                    }
                }
                
                chatButton.addEventListener('click', sendMessage);
                chatInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        sendMessage();
                    }
                });
            });
        </script>
        <style>
            .ai-chatbot {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 1000;
            }
            .chatbot-icon {
                width: 60px;
                height: 60px;
                background-color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            }
            .chatbot-popup {
                position: absolute;
                bottom: 80px;
                right: 0;
                width: 300px;
                background-color: white;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                overflow: hidden;
            }
            .chatbot-header {
                background-color: #F7CAC9;
                color: white;
                padding: 10px 15px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .chatbot-header h4 {
                margin: 0;
            }
            .close-chat {
                cursor: pointer;
                font-size: 20px;
            }
            .chatbot-messages {
                height: 300px;
                overflow-y: auto;
                padding: 15px;
            }
            .message {
                margin-bottom: 10px;
                padding: 8px 12px;
                border-radius: 18px;
                max-width: 80%;
                word-wrap: break-word;
            }
            .bot {
                background-color: #f1f1f1;
                align-self: flex-start;
                border-bottom-left-radius: 5px;
            }
            .user {
                background-color: #F7CAC9;
                color: white;
                margin-left: auto;
                border-bottom-right-radius: 5px;
            }
            .chatbot-input {
                display: flex;
                padding: 10px;
                border-top: 1px solid #eee;
            }
            .chatbot-input input {
                flex: 1;
                padding: 8px 12px;
                border: 1px solid #ddd;
                border-radius: 20px;
                outline: none;
            }
            .chatbot-input button {
                background-color: #F7CAC9;
                color: white;
                border: none;
                padding: 8px 15px;
                margin-left: 5px;
                border-radius: 20px;
                cursor: pointer;
            }
        </style>
    </div>
    <!-- /wp:html -->
    """
    
    # Update homepage content to include AI features
    print("Adding AI product recommendations and chatbot to homepage...")
    
    # Check if homepage exists
    response = requests.get(
        f"{WP_API}/wp/v2/pages?slug=home",
        headers=headers
    )
    
    if response.status_code == 200:
        pages = response.json()
        if pages:
            # Update existing homepage with AI features
            homepage_id = pages[0]["id"]
            current_content = pages[0]["content"]["raw"]
            
            # Add AI features before the closing tag of the last group
            updated_content = current_content.replace("<!-- /wp:group -->", "<!-- /wp:group -->\n\n" + ai_widget_content)
            
            response = requests.post(
                f"{WP_API}/wp/v2/pages/{homepage_id}",
                headers=headers,
                json={
                    "content": updated_content
                }
            )
            
            if response.status_code == 200:
                print("✓ AI features added to homepage")
            else:
                print(f"✗ Failed to add AI features: {response.status_code}")
                print(response.text)
        else:
            print("✗ Homepage not found")
    else:
        print(f"✗ Failed to check for homepage: {response.status_code}")
        print(response.text)

def main():
    """Main function to run the customization script"""
    print("WordPress Customization Script for Loja Dropshipping")
    print("===================================================")
    
    # Get authentication token
    headers = get_auth_token()
    if not headers:
        print("Authentication failed. Exiting.")
        sys.exit(1)
    
    # Add content type header
    headers["Content-Type"] = "application/json"
    
    # Run customization steps
    update_site_identity(headers)
    update_theme_mods(headers)
    update_custom_css(headers)
    setup_homepage(headers)
    setup_blog_page(headers)
    upload_logo(headers)
    setup_ai_features(headers)
    
    print("\n✅ Customization completed successfully!")
    print(f"Visit your site at: {WP_URL}")

if __name__ == "__main__":
    main()
