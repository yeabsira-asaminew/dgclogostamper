<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Logo_stamp extends CI_Controller
{

    public function index()
    {
        // Load the view for the upload form
        $this->load->view('logo-stamp');
    }

    public function upload_images()
    {
        // Disable CI's profiler and debug output
        $this->output->enable_profiler(FALSE);
        ini_set('display_errors', '0');
    
        try {
            // Check for file uploads
            if (empty($_FILES['images']['name'][0])) {
                throw new Exception("No images selected.");
            }
    
            // Validate required files
            $logo_path = FCPATH . 'assets/images/logo.png';
            $watermark_path = FCPATH . 'assets/images/logo.png'; // Same logo for watermark
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
                    $icon_spacing = 10;
                } elseif ($image_height >= 550 && $image_height < 1000) {
                    $rectangle_height = 55;
                    $logo_height = 60;
                    $icon_size = 35;
                    $icon_spacing = 15;
                } elseif ($image_height >= 1000 && $image_height < 2000) {
                    $rectangle_height = 65;
                    $logo_height = 70;
                    $icon_size = 40;
                    $icon_spacing = 20;
                } elseif ($image_height >= 2000 && $image_height < 3000) {
                    $rectangle_height = 75;
                    $logo_height = 80;
                    $icon_size = 45;
                    $icon_spacing = 25;
                } elseif ($image_height >= 3000 && $image_height < 4000) {
                    $rectangle_height = 85;
                    $logo_height = 90;
                    $icon_size = 50;
                    $icon_spacing = 30;
                } elseif ($image_height >= 4000 && $image_height < 5000) {
                    $rectangle_height = 95;
                    $logo_height = 100;
                    $icon_size = 60;
                    $icon_spacing = 35;
                }  elseif ($image_height >= 4000 && $image_height < 5000) {
                    $rectangle_height = 105;
                    $logo_height = 110;
                    $icon_size = 70;
                    $icon_spacing = 40;
                }  else {
                    $rectangle_height = 115;
                    $logo_height = 120;
                    $icon_size = 80;
                    $icon_spacing = 50;
                }
    
                // Adjust logo position based on width
                if ($image_width < 750) {
                    $logo_x = 50;
                } elseif ($image_width >= 750 && $image_width < 1500) {
                    $logo_x = 100;
                } elseif ($image_width >= 1500 && $image_width < 2500) {
                    $logo_x = 150;
                } else {
                    $logo_x = 200;
                }
    
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
    
                // Stamp main logo (bottom left)
                $stamp = imagecreatefrompng($logo_path);
                list($stamp_width, $stamp_height) = getimagesize($logo_path);
                $logo_scale = $logo_height / $stamp_height;
                $logo_new_height = $stamp_height * $logo_scale;
                $logo_new_width = $stamp_width * $logo_scale;
                $logo_y = $image_height - $logo_new_height;
                imagecopyresampled($image, $stamp, $logo_x, $logo_y, 0, 0, $logo_new_width, $logo_new_height, $stamp_width, $stamp_height);
    
                // Add transparent watermark logo (center)
                $watermark_stamp = imagecreatefrompng($watermark_path);
                if ($watermark_stamp) {
                    $watermark_height = $image_height * 0.2; // 20% of image height
                    $watermark_scale = $watermark_height / $stamp_height;
                    $watermark_new_width = $stamp_width * $watermark_scale;
                    $watermark_new_height = $stamp_height * $watermark_scale;
                    $watermark_x = ($image_width - $watermark_new_width) / 2;
                    $watermark_y = ($image_height * 0.8) - ($watermark_new_height / 2);
                    
                    // Create a truecolor image with alpha channel
                    $watermark = imagecreatetruecolor($watermark_new_width, $watermark_new_height);
                    imagealphablending($watermark, false);
                    imagesavealpha($watermark, true);
                    
                    // Fill with transparent color
                    $transparent = imagecolorallocatealpha($watermark, 255, 255, 255, 127);
                    imagefilledrectangle($watermark, 0, 0, $watermark_new_width, $watermark_new_height, $transparent);
                    
                    // Preserve alpha channel from original
                    imagealphablending($watermark_stamp, true);
                    imagesavealpha($watermark_stamp, true);
                    
                    // Resize with transparency
                    imagecopyresampled(
                        $watermark,
                        $watermark_stamp,
                        0, 0, 0, 0,
                        $watermark_new_width,
                        $watermark_new_height,
                        $stamp_width,
                        $stamp_height
                    );
                    
                    // Apply 30% opacity to the entire watermark
                    for ($x = 0; $x < $watermark_new_width; $x++) {
                        for ($y = 0; $y < $watermark_new_height; $y++) {
                            $color = imagecolorat($watermark, $x, $y);
                            $alpha = ($color >> 24) & 0xFF;
                            $r = ($color >> 16) & 0xFF;
                            $g = ($color >> 8) & 0xFF;
                            $b = $color & 0xFF;
                            
                            // Only modify non-transparent pixels
                            if ($alpha < 127) {
                                $new_alpha = min(127, $alpha + 89); // 30% opacity (127 * 0.3 ≈ 38, but inverted)
                                $new_color = imagecolorallocatealpha($watermark, $r, $g, $b, $new_alpha);
                                imagesetpixel($watermark, $x, $y, $new_color);
                            }
                        }
                    }
                    
                    // Merge with main image
                    imagealphablending($image, true);
                    imagecopy(
                        $image,
                        $watermark,
                        $watermark_x,
                        $watermark_y,
                        0,
                        0,
                        $watermark_new_width,
                        $watermark_new_height
                    );
                    
                    imagedestroy($watermark);
                }
                imagedestroy($watermark_stamp);
    
                // Add social media icons
                $total_icons_width = (count($social_icons) * $icon_size) + ((count($social_icons) - 1) * $icon_spacing);
                $start_x = $rectangle_x + ($rectangle_width - $total_icons_width) / 2;
    
                foreach ($social_icons as $icon_path) {
                    $icon = imagecreatefrompng($icon_path);
                    imagecopyresampled(
                        $image,
                        $icon,
                        $start_x,
                        $rectangle_y + ($rectangle_height - $icon_size) / 2,
                        0,
                        0,
                        $icon_size,
                        $icon_size,
                        imagesx($icon),
                        imagesy($icon)
                    );
                    $start_x += $icon_size + $icon_spacing;
                    imagedestroy($icon);
                }
    
                // Add text (font size scales with icon size)
                $font_size = $icon_size * 0.35;
                $black = imagecolorallocate($image, 0, 0, 0);
                $text_x = $start_x + $icon_spacing;
    
                // Calculate total text block height
                $total_text_height = (count($texts) * $font_size) + ((count($texts) - 1) * 5);
                $text_y = $rectangle_y + ($rectangle_height - $total_text_height) / 2 + $font_size;
    
                foreach ($texts as $text) {
                    imagettftext($image, $font_size, 0, $text_x, $text_y, $black, $font_path, $text);
                    $text_y += $font_size + 5;
                }
    
                // Capture image output
                ob_start();
                if ($image_ext === 'png') {
                    imagepng($image);
                } else {
                    imagejpeg($image, NULL, 90);
                }
                $image_data = ob_get_clean();
    
                $stamped_images[] = [
                    'name' => $image_name,
                    'data' => $image_data,
                    'type' => ($image_ext === 'png') ? 'image/png' : 'image/jpeg'
                ];
    
                imagedestroy($image);
                imagedestroy($stamp);
            }
    
            if (empty($stamped_images)) {
                throw new Exception("No valid images were processed.");
            }
    
            // Return images as downloadable links
            $result = [
                'status' => 'success',
                'message' => count($stamped_images) . ' images stamped successfully!',
                'images' => array_map(function ($img) {
                    return [
                        'name' => $img['name'],
                        'url' => 'data:' . $img['type'] . ';base64,' . base64_encode($img['data'])
                    ];
                }, $stamped_images)
            ];
    
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($result));
        } catch (Exception $e) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]));
        }
    }
}
