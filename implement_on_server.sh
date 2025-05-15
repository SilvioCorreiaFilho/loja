#!/bin/zsh
# Script to implement WordPress customizations on the server
# Compatible with Mac zsh

echo "WordPress Customization Implementation"
echo "===================================="

# Server details
SERVER="167.86.115.59"
USER="root"
PASSWORD="Malasf1980M2w"
WP_DIR="/var/www/html"

echo "Connecting to server and implementing customizations..."

# Create a temporary script to run on the server
cat > temp_customize.sh << 'EOL'
#!/bin/bash
# Script to implement WordPress customizations using WP-CLI

# Navigate to WordPress directory
cd /var/www/html

echo "Starting WordPress customization implementation..."

# 1. Site Identity
echo "Updating site identity..."
wp option update blogname "Loja Dropshipping" --allow-root
wp option update blogdescription "Beleza e Cuidados Essenciais" --allow-root

# 2. Theme Mods - Colors
echo "Updating color scheme..."
wp theme mod set primary_color "#E8D2D2" --allow-root
wp theme mod set secondary_color "#D4E2D4" --allow-root
wp theme mod set accent_color "#F7CAC9" --allow-root
wp theme mod set text_color "#555555" --allow-root
wp theme mod set background_color "#FFFFFF" --allow-root
wp theme mod set button_background_color "#F7CAC9" --allow-root
wp theme mod set button_text_color "#FFFFFF" --allow-root
wp theme mod set button_hover_background_color "#E8D2D2" --allow-root
wp theme mod set button_hover_text_color "#FFFFFF" --allow-root
wp theme mod set header_background_color "#FFFFFF" --allow-root
wp theme mod set header_text_color "#555555" --allow-root
wp theme mod set footer_background_color "#F9F9F9" --allow-root
wp theme mod set footer_text_color "#555555" --allow-root

# 3. Typography
echo "Updating typography..."
wp theme mod set body_font_family "Open Sans" --allow-root
wp theme mod set heading_font_family "Montserrat" --allow-root
wp theme mod set menu_font_family "Montserrat" --allow-root
wp theme mod set body_font_size "16px" --allow-root
wp theme mod set h1_font_size "36px" --allow-root
wp theme mod set h2_font_size "30px" --allow-root
wp theme mod set h3_font_size "24px" --allow-root
wp theme mod set h4_font_size "20px" --allow-root
wp theme mod set h5_font_size "18px" --allow-root
wp theme mod set h6_font_size "16px" --allow-root

# 4. Custom CSS
echo "Adding custom CSS..."
CSS=$(cat <<'CSSEOF'
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

/* AI features styling */
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
CSSEOF
)

echo "$CSS" > custom_css.txt
wp theme mod set custom_css "$(cat custom_css.txt)" --allow-root
rm custom_css.txt

# 5. Download and set logo
echo "Setting up logo..."
wget -O logo.png https://i.imgur.com/8jIGpEX.png
LOGO_ID=$(wp media import logo.png --title="Site Logo" --featured --porcelain --allow-root)
wp theme mod set custom_logo $LOGO_ID --allow-root
rm logo.png

# 6. Create or update homepage
echo "Setting up homepage..."
# Check if homepage exists
HOME_ID=$(wp post list --post_type=page --name=home --field=ID --allow-root)
if [ -z "$HOME_ID" ]; then
  # Create homepage
  HOME_ID=$(wp post create --post_type=page --post_title="Home" --post_status=publish --porcelain --allow-root)
  echo "Created new homepage with ID: $HOME_ID"
else
  echo "Using existing homepage with ID: $HOME_ID"
fi

# Set as front page
wp option update show_on_front page --allow-root
wp option update page_on_front $HOME_ID --allow-root

# 7. Create or update blog page
echo "Setting up blog page..."
# Check if blog page exists
BLOG_ID=$(wp post list --post_type=page --name=blog --field=ID --allow-root)
if [ -z "$BLOG_ID" ]; then
  # Create blog page
  BLOG_ID=$(wp post create --post_type=page --post_title="Blog" --post_status=publish --porcelain --allow-root)
  echo "Created new blog page with ID: $BLOG_ID"
else
  echo "Using existing blog page with ID: $BLOG_ID"
fi

# Set as posts page
wp option update page_for_posts $BLOG_ID --allow-root

# 8. Add AI features to homepage
echo "Adding AI features to homepage..."
AI_FEATURES=$(cat <<'AIEOF'
<!-- wp:html -->
<div class="ai-product-recommendations">
    <h3>Recomendações Personalizadas</h3>
    <div class="ai-recommendation-container">
        <script>
            // AI Product Recommendation Script
            document.addEventListener("DOMContentLoaded", function() {
                // This would be replaced with actual AI recommendation logic
                console.log("AI product recommendation system initialized");
                
                // Simulate AI recommendations with placeholder
                const recommendationContainer = document.querySelector(".ai-recommendation-container");
                
                // In a real implementation, this would call an API to get personalized recommendations
                const products = [
                    {name: "Sérum Facial", price: "R$ 89,90", image: "https://via.placeholder.com/150"},
                    {name: "Creme Hidratante", price: "R$ 69,90", image: "https://via.placeholder.com/150"},
                    {name: "Máscara Facial", price: "R$ 49,90", image: "https://via.placeholder.com/150"}
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
                html += "</div>";
                
                recommendationContainer.innerHTML = html;
            });
        </script>
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
        document.addEventListener("DOMContentLoaded", function() {
            const chatbotIcon = document.querySelector(".chatbot-icon");
            const chatbotPopup = document.querySelector(".chatbot-popup");
            const closeChat = document.querySelector(".close-chat");
            const chatInput = document.querySelector(".chatbot-input input");
            const chatButton = document.querySelector(".chatbot-input button");
            const chatMessages = document.querySelector(".chatbot-messages");
            
            // Toggle chatbot popup
            chatbotIcon.addEventListener("click", function() {
                chatbotPopup.style.display = "block";
            });
            
            closeChat.addEventListener("click", function() {
                chatbotPopup.style.display = "none";
            });
            
            // Handle sending messages
            function sendMessage() {
                const message = chatInput.value.trim();
                if (message) {
                    // Add user message
                    chatMessages.innerHTML += `<div class="message user">${message}</div>`;
                    chatInput.value = "";
                    
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
            
            chatButton.addEventListener("click", sendMessage);
            chatInput.addEventListener("keypress", function(e) {
                if (e.key === "Enter") {
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
AIEOF
)

echo "$AI_FEATURES" > ai_features.txt

# Get current homepage content
CURRENT_CONTENT=$(wp post get $HOME_ID --field=content --allow-root)

# Add AI features to homepage content
wp post update $HOME_ID --post-content="$CURRENT_CONTENT $(<ai_features.txt)" --allow-root
rm ai_features.txt

echo "✅ WordPress customization completed successfully!"
echo "Visit your site to see the changes."
EOL

# Make the script executable
chmod +x temp_customize.sh

# Use sshpass to connect and run the script
# First check if sshpass is installed
if ! command -v sshpass &> /dev/null; then
    echo "❌ sshpass is not installed. Please install it with: brew install sshpass"
    echo "You may need to run: brew tap esolitos/ipa"
    echo "Then: brew install sshpass"
    exit 1
fi

# Upload and execute the script
sshpass -p "$PASSWORD" scp temp_customize.sh $USER@$SERVER:/tmp/
sshpass -p "$PASSWORD" ssh $USER@$SERVER "bash /tmp/temp_customize.sh"

# Clean up
rm temp_customize.sh

echo "✅ Customization implementation completed!"
