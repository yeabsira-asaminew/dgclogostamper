<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logo_stamp extends CI_Controller {

    public function index() {
        // Load the view for the upload form
        $this->load->view('logo-stamp');
    }

    public function upload_images() {
        // Disable CI's profiler and debug output
        $this->output->enable_profiler(FALSE);
        
        // Ensure no PHP errors are output
        ini_set('display_errors', '0');
        
        // Set JSON header FIRST
        header('Content-Type: application/json');
        
        try {
            // Check for file uploads
            if (empty($_FILES['images']['name'][0])) {
                throw new Exception("No images selected.");
            }
    
            // Validate output folder
            $output_folder = $this->input->post('output_folder');
            if (empty($output_folder)) {
                throw new Exception("Output folder not specified.");
            }
    
            // Create folder (skip if exists)
            if (!file_exists($output_folder) && !mkdir($output_folder, 0777, TRUE)) {
                throw new Exception("Failed to create output directory.");
            }
    
            // Validate required files
            $logo_path = FCPATH . 'assets/images/logo.png';
            $font_path = FCPATH . 'application/fonts/AbyssinicaSIL-R.ttf';
            
            if (!file_exists($logo_path)) {
                throw new Exception("Logo file not found.");
            }
            if (!file_exists($font_path)) {
                throw new Exception("Font file not found.");
            }
    
            // Social icons
            $social_icons = [
                FCPATH . 'assets/images/facebook.png',
                FCPATH . 'assets/images/youtube.png',
                FCPATH . 'assets/images/telegram.png',
                FCPATH . 'assets/images/tiktok.png',
                FCPATH . 'assets/images/twitter.png'
            ];
            
            // Validate social icons
            foreach ($social_icons as $icon) {
                if (!file_exists($icon)) {
                    throw new Exception("Social icon not found: " . basename($icon));
                }
            }
    
            // Text content
            $texts = ['ይወዳጁን!', 'Firoomaa!', 'Naguso biraa!'];
    
            // Process images
            $stamped_images = [];
            foreach ($_FILES['images']['tmp_name'] as $index => $tmp_name) {
                $image_path = $tmp_name;
                $image_name = $_FILES['images']['name'][$index];
                $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
    
                // Skip invalid formats
                if (!in_array($image_ext, ['jpg', 'jpeg', 'png'])) continue;
    
                // Load image
                $image = ($image_ext === 'png') 
                    ? @imagecreatefrompng($image_path) 
                    : @imagecreatefromjpeg($image_path);
    
                if (!$image) continue;
    
                list($image_width, $image_height) = getimagesize($image_path);
    
                // Determine sizes based on image dimensions
                if ($image_height < 550) {
                    $rectangle_height = 45;
                    $logo_height = 50;
                    $icon_size = 30;
                } elseif ($image_height >= 550 && $image_height < 1000) {
                    $rectangle_height = 55;
                    $logo_height = 60;
                    $icon_size = 35;
                } elseif ($image_height >= 1000 && $image_height < 2000) {
                    $rectangle_height = 65;
                    $logo_height = 70;
                    $icon_size = 40;
                } else {
                    $rectangle_height = 75;
                    $logo_height = 80;
                    $icon_size = 45;
                }
    
                // Adjust logo position based on width
                $logo_x = ($image_width < 750) ? 50 : 100;
    
                // Create rectangle with dot pattern
                $rectangle_width = $image_width;
                $rectangle_x = ($image_width - $rectangle_width) / 2;
                $rectangle_y = $image_height - $rectangle_height;
    
                // Dot pattern
                $dot_size = 3;
                $dot_spacing = 2;
                $cols = ceil($rectangle_width / $dot_spacing);
                $rows = ceil($rectangle_height / $dot_spacing);
    
                for ($i = 0; $i < $cols; $i++) {
                    for ($j = 0; $j < $rows; $j++) {
                        $transparency = 25 + (($i / $cols) * 65);
                        $x = $rectangle_x + ($i * $dot_spacing);
                        $y = $rectangle_y + ($j * $dot_spacing);
                        
                        if ($x <= ($rectangle_x + $rectangle_width) && $y <= ($rectangle_y + $rectangle_height)) {
                            $white = imagecolorallocatealpha($image, 255, 255, 255, (int)$transparency);
                            imagefilledellipse($image, $x, $y, $dot_size, $dot_size, $white);
                        }
                    }
                }
    
                // Stamp logo
                $stamp = imagecreatefrompng($logo_path);
                list($stamp_width, $stamp_height) = getimagesize($logo_path);
                $logo_scale = $logo_height / $stamp_height;
                $logo_new_height = $stamp_height * $logo_scale;
                $logo_new_width = $stamp_width * $logo_scale;
                $logo_y = $image_height - $logo_new_height;
                imagecopyresampled($image, $stamp, $logo_x, $logo_y, 0, 0, $logo_new_width, $logo_new_height, $stamp_width, $stamp_height);
    
                // Add social media icons
                $icon_spacing = 10;
                $total_icons_width = (count($social_icons) * $icon_size) + ((count($social_icons) - 1) * $icon_spacing);
                $start_x = $rectangle_x + ($rectangle_width - $total_icons_width) / 2;
    
                foreach ($social_icons as $icon_path) {
                    $icon = imagecreatefrompng($icon_path);
                    imagecopyresampled($image, $icon, $start_x, $rectangle_y + ($rectangle_height - $icon_size) / 2, 
                        0, 0, $icon_size, $icon_size, imagesx($icon), imagesy($icon));
                    $start_x += $icon_size + $icon_spacing;
                    imagedestroy($icon);
                }
    
                // Add text (font size scales with icon size)
                $font_size = $icon_size * 0.35; // Proportional to icon size
                $black = imagecolorallocate($image, 0, 0, 0);
                $text_x = $start_x + $icon_spacing;
                
                // Calculate total text block height
                $total_text_height = (count($texts) * $font_size) + ((count($texts) - 1) * 5);
                $text_y = $rectangle_y + ($rectangle_height - $total_text_height) / 2 + $font_size;
    
                foreach ($texts as $text) {
                    imagettftext($image, $font_size, 0, $text_x, $text_y, $black, $font_path, $text);
                    $text_y += $font_size + 5;
                }
    
                // Save stamped image
                $output_path = rtrim($output_folder, '/') . '/' . $image_name;
                if ($image_ext === 'png') {
                    imagepng($image, $output_path);
                } else {
                    imagejpeg($image, $output_path, 90);
                }
    
                imagedestroy($image);
                imagedestroy($stamp);
                $stamped_images[] = $output_path;
            }
    
            if (empty($stamped_images)) {
                throw new Exception("No valid images were processed.");
            }
    
            // SUCCESS response
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => count($stamped_images) . ' images stamped successfully!',
                    'images' => $stamped_images
                ]));
    
        } catch (Exception $e) {
            // ERROR response
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]));
        }
    }
}





   
