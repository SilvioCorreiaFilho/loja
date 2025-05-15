# WordPress Customization for Loja Dropshipping

This project contains scripts to customize a WordPress site with the Biagiotti theme for a beauty/skincare dropshipping store. The customizations are based on detailed research and analysis of successful beauty store designs, with added AI features and custom branding.

## Files Included

- `wp_customizer.py`: Python script that uses the WordPress REST API to implement customizations
- `wordpress_customization_script.php`: PHP script that can be used with WP-CLI or directly in WordPress
- `run_customization.sh`: Shell script to easily run the Python customization script

## Customizations Applied

The scripts implement the following customizations based on the research analysis:

1. **Site Identity**
   - Site title: "Loja Dropshipping"
   - Tagline: "Beleza e Cuidados Essenciais"
   - Custom logo upload and implementation

2. **Color Scheme**
   - Primary palette of pastel colors (Rosa Suave e Menta Fresca)
   - Clean, elegant color combinations optimized for beauty products

3. **Typography**
   - Modern, sans-serif fonts (Montserrat for headings, Open Sans for body text)
   - Optimized font sizes for readability and elegance

4. **Homepage Layout**
   - Hero section with minimal text and strong call-to-action
   - Featured products section
   - About section focusing on brand values
   - Testimonials for social proof
   - Newsletter signup section

5. **Custom CSS**
   - Clean, minimal design with ample white space
   - Enhanced product displays
   - Subtle animations and hover effects
   - Mobile-responsive design

6. **AI Features**
   - AI-powered product recommendations section
   - Interactive chatbot for customer support
   - Personalized shopping experience

## How to Use

### Option 1: Using the Python Script (Recommended)

1. Make the shell script executable:
   ```
   chmod +x run_customization.sh
   ```

2. Run the shell script:
   ```
   ./run_customization.sh
   ```

   This will:
   - Check for Python and required packages
   - Install any missing packages
   - Run the Python customization script

### Option 2: Using the PHP Script

#### With WP-CLI:

1. Upload the PHP script to your WordPress site
2. Run the script using WP-CLI:
   ```
   wp eval-file wordpress_customization_script.php
   ```

#### Without WP-CLI:

1. Upload the PHP script to your WordPress site
2. Include the script in a PHP file that runs in the WordPress context:
   ```php
   include_once('wordpress_customization_script.php');
   ```

## Customization Details

The customizations are based on research of successful beauty/skincare stores and follow these principles:

- **Clean, Minimal Design**: Focuses attention on products with ample white space
- **Pastel Color Palette**: Soft, feminine colors that convey elegance and quality
- **Modern Typography**: Sans-serif fonts for a contemporary feel
- **Strategic Layout**: Organized to maximize conversions with clear CTAs
- **Mobile-First Approach**: Fully responsive design for all devices

## Requirements

- Python 3 and pip (for the Python script)
- WordPress with the Biagiotti theme installed
- Admin access to the WordPress site

## Notes

- The scripts use the credentials provided in the files. Update them if needed.
- Some theme-specific customizations may need adjustments depending on the exact version of the Biagiotti theme.
- After running the scripts, you may need to add actual product images and content.
