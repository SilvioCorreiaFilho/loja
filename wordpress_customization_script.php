<?php
/**
 * WordPress Customization Script for Loja Dropshipping
 * Based on research analysis of Biagiotti theme and WooCommerce themes
 * Includes logo upload and AI features
 */

// Check if this is being run in WordPress context or standalone
if (!defined('ABSPATH')) {
    // For standalone execution, you'll need to provide credentials
    $wp_api_url = 'https://wordpress-tc48ws4ccowwwc8s0sockooo.m2w.io/wp-json';
    $username = 'silvio@m2w.digital';
    $password = 'Malasf@1980@M2w'; // Replace with actual password if needed
    
    // Logo URL - Replace with your actual logo URL
    $logo_url = 'https://i.imgur.com/8jIGpEX.png'; // Default placeholder logo
    
    // Function to get authentication token
    function get_wp_token($api_url, $username, $password) {
        $auth_url = $api_url . '/jwt-auth/v1/token';
        $response = wp_remote_post($auth_url, [
            'body' => [
                'username' => $username,
                'password' => $password
            ]
        ]);
        
        if (is_wp_error($response)) {
            echo "Authentication error: " . $response->get_error_message();
            return false;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($body['token'])) {
            return $body['token'];
        }
        
        echo "Authentication failed: " . print_r($body, true);
        return false;
    }
    
    // Include WordPress API if running standalone
    if (!function_exists('wp_remote_post')) {
        echo "This script needs to be run within WordPress or with WP-CLI.\n";
        exit(1);
    }
}

/**
 * Main customization function
 */
function customize_loja_dropshipping() {
    echo "Starting customization of Loja Dropshipping...\n";
    
    // 1. Site Identity
    update_option('blogname', 'Loja Dropshipping');
    update_option('blogdescription', 'Beleza e Cuidados Essenciais');
    echo "Site identity updated.\n";
    
    // Upload and set logo
    upload_and_set_logo();
    
    // 2. Color Scheme - Based on the pastel palette recommendation
    $theme_mods = [
        // Primary color palette (Rosa Suave e Menta Fresca)
        'primary_color' => '#E8D2D2', // Rosa Pastel Queimado
        'secondary_color' => '#D4E2D4', // Verde Menta Pastel
        'accent_color' => '#F7CAC9', // Rosa Bebê
        'text_color' => '#555555', // Cinza Escuro
        'background_color' => '#FFFFFF', // Branco
        
        // Button colors
        'button_background_color' => '#F7CAC9', // Rosa Bebê
        'button_text_color' => '#FFFFFF', // Branco
        'button_hover_background_color' => '#E8D2D2', // Rosa Pastel Queimado
        'button_hover_text_color' => '#FFFFFF', // Branco
        
        // Header colors
        'header_background_color' => '#FFFFFF', // Branco
        'header_text_color' => '#555555', // Cinza Escuro
        
        // Footer colors
        'footer_background_color' => '#F9F9F9', // Cinza muito claro
        'footer_text_color' => '#555555', // Cinza Escuro
    ];
    
    foreach ($theme_mods as $key => $value) {
        set_theme_mod($key, $value);
    }
    echo "Color scheme updated.\n";
    
    // 3. Typography - Modern, sans-serif fonts
    $typography_settings = [
        'body_font_family' => 'Open Sans',
        'heading_font_family' => 'Montserrat',
        'menu_font_family' => 'Montserrat',
        'body_font_size' => '16px',
        'h1_font_size' => '36px',
        'h2_font_size' => '30px',
        'h3_font_size' => '24px',
        'h4_font_size' => '20px',
        'h5_font_size' => '18px',
        'h6_font_size' => '16px',
    ];
    
    foreach ($typography_settings as $key => $value) {
        set_theme_mod($key, $value);
    }
    echo "Typography updated.\n";
    
    // 4. Create a static homepage if it doesn't exist
    $homepage = get_page_by_title('Home');
    if (!$homepage) {
        // Create homepage
        $homepage_id = wp_insert_post([
            'post_title' => 'Home',
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'page',
        ]);
        
        // Set as static homepage
        update_option('show_on_front', 'page');
        update_option('page_on_front', $homepage_id);
        
        echo "Homepage created and set as front page.\n";
    } else {
        // Set existing page as homepage
        update_option('show_on_front', 'page');
        update_option('page_on_front', $homepage->ID);
        echo "Existing homepage set as front page.\n";
    }
    
    // Create a blog page if it doesn't exist
    $posts_page = get_page_by_title('Blog');
    if (!$posts_page) {
        $posts_page_id = wp_insert_post([
            'post_title' => 'Blog',
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'page',
        ]);
        
        update_option('page_for_posts', $posts_page_id);
        echo "Blog page created and set as posts page.\n";
    } else {
        update_option('page_for_posts', $posts_page->ID);
        echo "Existing blog page set as posts page.\n";
    }
    
    // 5. Additional CSS for clean, minimal design
    $custom_css = '
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
    ';
    
    // Add custom CSS
    wp_update_custom_css_post($custom_css);
    echo "Custom CSS added.\n";
    
    echo "Customization completed successfully!\n";
}

/**
 * Upload and set the site logo
 */
function upload_and_set_logo() {
    global $logo_url;
    
    echo "Uploading and setting site logo...\n";
    
    // Check if we're in WordPress context
    if (function_exists('wp_get_upload_dir')) {
        try {
            // Download the logo image
            $logo_data = file_get_contents($logo_url);
            if ($logo_data === false) {
                echo "Failed to download logo from URL.\n";
                return;
            }
            
            // Prepare upload directory
            $upload_dir = wp_upload_dir();
            $logo_filename = 'logo-' . time() . '.png';
            $logo_path = $upload_dir['path'] . '/' . $logo_filename;
            
            // Save the file
            file_put_contents($logo_path, $logo_data);
            
            // Prepare attachment data
            $filetype = wp_check_filetype($logo_filename, null);
            $attachment = array(
                'post_mime_type' => $filetype['type'],
                'post_title' => sanitize_file_name($logo_filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            
            // Insert the attachment
            $attach_id = wp_insert_attachment($attachment, $logo_path);
            
            // Generate metadata for the attachment
            $attach_data = wp_generate_attachment_metadata($attach_id, $logo_path);
            wp_update_attachment_metadata($attach_id, $attach_data);
            
            // Set as site logo
            set_theme_mod('custom_logo', $attach_id);
            
            echo "Logo uploaded and set successfully (ID: $attach_id).\n";
        } catch (Exception $e) {
            echo "Error uploading logo: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Cannot upload logo in standalone mode. Please run this script within WordPress context.\n";
    }
}

/**
 * Add AI features to the homepage
 */
function add_ai_features() {
    echo "Adding AI features to homepage...\n";
    
    // Check if homepage exists
    $homepage = get_page_by_title('Home');
    if (!$homepage) {
        echo "Homepage not found. Cannot add AI features.\n";
        return;
    }
    
    // AI features content
    $ai_features_content = '
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
                    
                    let html = \'<div class="ai-products">\';
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
    ';
    
    // Get current content and append AI features
    $current_content = $homepage->post_content;
    $updated_content = $current_content . $ai_features_content;
    
    // Update the homepage with AI features
    $update_args = array(
        'ID' => $homepage->ID,
        'post_content' => $updated_content
    );
    
    $result = wp_update_post($update_args);
    if ($result) {
        echo "AI features added to homepage successfully.\n";
    } else {
        echo "Failed to add AI features to homepage.\n";
    }
}

// Execute the customization
customize_loja_dropshipping();

// Add AI features after homepage is set up
add_ai_features();

/**
 * WP-CLI command to run this script
 * Usage: wp eval-file wordpress_customization_script.php
 */
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::success('Customization script executed via WP-CLI.');
}
?>
