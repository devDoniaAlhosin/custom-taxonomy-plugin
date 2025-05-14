# Custom Portfolio Filter Plugin Setup Guide

## Local WordPress Installation

1. **Install XAMPP**
   - Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Install XAMPP on your computer
   - Start Apache and MySQL services from XAMPP Control Panel

2. **Download WordPress**
   - Download WordPress from [https://wordpress.org/download/](https://wordpress.org/download/)
   - Extract the WordPress files
   - Copy the contents to `C:\xampp\htdocs\your-project-name`

3. **Create Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `your_database_name`
   - Note down the database name, username (default: root), and password (default: empty)

4. **Install WordPress**
   - Visit http://localhost/your-project-name
   - Follow the WordPress installation wizard
   - Enter your database details
   - Complete the installation

## Theme Installation

1. **Install Salient Real Estate Theme**
   - Log in to WordPress admin panel
   - Go to Appearance > Themes
   - Click "Add New"
   - Click "Upload Theme"
   - add the Salient Real Estate theme 
   - Activate the theme

## Plugin Installation
1. **Download Plugin from GitHub**
   - Visit the plugin's GitHub repository
   - Select "Download ZIP"
   - Save the ZIP file to your computer
2. **Install Custom Portfolio Filter Plugin**
   - Go to Plugins > Add New
   - Click "Upload Plugin"
   - Upload the `custom-portfolio-filter.zip` file
   - Activate the plugin

## Setting Up Portfolio

1. **Create Categories**
   - Go to Portfolio > Categories
   - Add your project categories (e.g., Residential, Commercial, Industrial)
   - Add descriptions if needed
   - Click "Add New Category"

2. **Create Services**
   - Go to Portfolio > Services
   - Add your service types (e.g., Architecture, Interior Design, Construction)
   - Add descriptions if needed
   - Click "Add New Service"

3. **Add Portfolio Items**
   - Go to Portfolio > Add New
   - Enter a title for your portfolio item
   - Add a featured image
   - Write a description
   - Select categories and services
   - Click "Publish"

4. **Create Portfolio Page**
   - Go to Pages > Add New
   - Give it a title (e.g., "Our Portfolio")
   - Add the shortcode: `[portfolio_filter]`
   - For custom number of items and columns: `[portfolio_filter posts_per_page="12" columns="4"]`
   - Click "Publish"

## Shortcode Options

1. **Basic Usage**
   ```
   [portfolio_filter]
   ```
   - Shows 9 items per page
   - 3 columns layout

2. **Custom Layout**
   ```
   [portfolio_filter posts_per_page="12" columns="4"]
   ```
   - Customize number of items per page
   - Customize number of columns

 **Dummy Data Generation**
   - The plugin automatically generates sample data on first activation
