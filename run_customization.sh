#!/bin/bash
# Script to run WordPress customization

echo "WordPress Customization Runner"
echo "============================"
echo

# Check if Python is installed
if ! command -v python3 &> /dev/null; then
    echo "❌ Python 3 is not installed. Please install Python 3 and try again."
    exit 1
fi

echo "✅ Python 3 is installed."

# Create and activate virtual environment
echo "Creating virtual environment..."
python3 -m venv venv
source venv/bin/activate

# Check if virtual environment is activated
if [[ "$VIRTUAL_ENV" == "" ]]; then
    echo "❌ Failed to activate virtual environment. Please check your Python installation."
    exit 1
fi

echo "✅ Virtual environment created and activated."

# Install required packages in the virtual environment
echo "Installing required Python packages..."
pip install requests
echo "✅ Packages installed."

# Make the Python script executable
chmod +x wp_customizer.py

# Run the Python script
echo "Running WordPress customization script..."
python wp_customizer.py

# Deactivate virtual environment
deactivate

echo
echo "Script execution completed."
