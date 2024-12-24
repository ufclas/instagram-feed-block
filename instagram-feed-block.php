<?php
/**
 * Plugin Name: Instagram Feed Block
 * Description: A Gutenberg block to fetch and display Instagram posts.
 * Version: 0.1.0
 * Author: The WordPress Contributors
 */

if (!defined('ABSPATH')) {
    exit;
}

function enqueue_instagram_block_script() {
    wp_enqueue_script(
        'instagram-feed-block',
        plugins_url('block.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-api', 'wp-editor'),
        true
    );
	wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', array(), '6.5.1', 'all');
}

add_action('enqueue_block_editor_assets', 'enqueue_instagram_block_script');

function render_instagram_feed_block($attributes) {
    $access_token = isset($attributes['accessToken']) ? $attributes['accessToken'] : '';
    $limit = isset($attributes['limit']) ? absint($attributes['limit']) : 4;
	$sectionheading = isset($attributes['sectionHeading']) ? $attributes['sectionHeading'] : '';
	$fburl = isset($attributes['fburl']) ? $attributes['fburl'] : '';
	$twitterurl = isset($attributes['twitterurl']) ? $attributes['twitterurl'] : '';
	$instagramurl = isset($attributes['instagramurl']) ? $attributes['instagramurl'] : '';
	$youtubeurl = isset($attributes['youtubeurl']) ? $attributes['youtubeurl'] : '';
	$output = '';
	$page_id = get_the_ID();
    if (!empty($access_token)) {
        $api_url = 'https://graph.instagram.com/me/media?fields=id,media_type,media_url,thumbnail_url,timestamp,permalink,caption&access_token=' . $access_token . '&limit=' . $limit;
			
        $response = wp_remote_get($api_url);
		
        if (!is_wp_error($response) && $response['response']['code'] == 200) {
			update_post_meta( $page_id, 'instagram_data', $response['body'] );
            $data = json_decode($response['body'], true);
			
            if (isset($data['data'])) {
                $output .= '<section class="social-media-shell"><div class="social-media-shell-wrapper"><p class="social-media-shell-title">'.$sectionheading.'</p><div class="social-feed row">';
                foreach ($data['data'] as $post) {
					// Check if the 'caption' key exists
                $caption = isset($post['caption']) ? esc_html($post['caption']) : '';
					if($post['media_type'] == 'IMAGE' || $post['media_type'] == 'CAROUSEL_ALBUM'){
					$media = esc_url($post['media_url']);
				}
				else{
					$media = esc_url($post['thumbnail_url']);
				}
                   /*  $output .= '<div>';
                    $output .= '<p>ID: ' . esc_html($post['id']) . '</p>';
                    $output .= '<p>Caption: ' . esc_html($post['caption']) . '</p>';
                    $output .= '<p>Media Type: ' . esc_html($post['media_type']) . '</p>';
                    $output .= '<p>Media URL: ' . esc_url($post['media_url']) . '</p>';
                    $output .= '<p>Thumbnail URL: ' . esc_url($post['thumbnail_url']) . '</p>';
                    $output .= '<p>Permalink: ' . esc_url($post['permalink']) . '</p>';
                    $output .= '<p>Timestamp: ' . esc_html($post['timestamp']) . '</p>';
                    $output .= '</div>'; */
					$output .='<div class="col-xl-3 col-md-6 col-12 soc-container">	<a href="'.esc_url($post['permalink']).'" target="_blank"
                            class="social-item m-insta social-overlay" style="background-image: url('.$media.'); ">
					<span class="social-text">'.$caption.'<span class="social-icon"><img src="'.get_stylesheet_directory_uri().'/img/icon-instagram.png" alt="Instagram Icon"></span></span>
					</a>

                        </div>';
                }
                $output .= '</div>';
				$output .= '<div class="col-12 social-column social-column-blue justify-content-center mt-5">';
				if(!empty($fburl) && $fburl != '#'){	
				$output .= '<a href="'.$fburl.'" target="_blank" class="facebook-icon"
                        rel="noopener">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="ufl-brands ufl-facebook"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg>
				<span class="visually-hidden">Facebook Icon</span>
			</a>';
				}
		if(!empty($twitterurl) && $twitterurl != '#'){		
		$output .= '<a href="'.$twitterurl.'" target="_blank" class="twitter-icon"
                        rel="noopener">
				<svg viewBox="0 0 1200 1227" fill="none" xmlns="http://www.w3.org/2000/svg" class="ufl-brands ufl-twitter"><path d="M714.163 519.284L1160.89 0H1055.03L667.137 450.887L357.328 0H0L468.492 681.821L0 1226.37H105.866L515.491 750.218L842.672 1226.37H1200L714.137 519.284H714.163ZM569.165 687.828L521.697 619.934L144.011 79.6944H306.615L611.412 515.685L658.88 583.579L1055.08 1150.3H892.476L569.165 687.854V687.828Z" fill="currentColor"/></svg>
				<span class="visually-hidden">Twitter Icon</span>
			</a>';
		}
		if(!empty($instagramurl) && $instagramurl != '#'){		
		$output .='<a href="'.$instagramurl.'" target="_blank" class="instagram-icon"
                        rel="noopener">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="ufl-brands ufl-instagram"><path fill="currentColor"  d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
				<span class="visually-hidden">Instagram Icon</span>
			</a>';
	}
		if(!empty($youtubeurl) && $youtubeurl != '#'){		
		$output .='<a href="'.$youtubeurl.'" target="_blank"
                        class="youtube-icon" rel="noopener">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="ufl-brands ufl-youtube"><path fill="currentColor"  d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z"/></svg>
				<span class="visually-hidden">Youtube Icon</span>
			</a>';
		}

                    $output .='</div>';
				$output .= '</div></section>';
            } else {
                $output = '<p>No Instagram posts found.</p>';
            }
        } else {
            //$output = '<p>Error fetching Instagram posts. Please check your access token and try again.</p>';
			//$output =  get_post_meta($page_id,'instagram_data',true);
			$metadata =  get_post_meta($page_id,'instagram_data',false);
			$data = json_decode($metadata[0], true);
			
            if (isset($data)) {
                $output .= '<section class="social-media-shell cdb"><div class="social-media-shell-wrapper"><p class="social-media-shell-title">'.$sectionheading.'</p><div class="social-feed row">';
                foreach ($data['data'] as $post) {
					// Check if the 'caption' key exists
                $caption = isset($post['caption']) ? esc_html($post['caption']) : '';
					if($post['media_type'] == 'IMAGE' || $post['media_type'] == 'CAROUSEL_ALBUM'){
					$media = esc_url($post['media_url']);
				}
				else{
					$media = esc_url($post['thumbnail_url']);
				}
                   /*  $output .= '<div>';
                    $output .= '<p>ID: ' . esc_html($post['id']) . '</p>';
                    $output .= '<p>Caption: ' . esc_html($post['caption']) . '</p>';
                    $output .= '<p>Media Type: ' . esc_html($post['media_type']) . '</p>';
                    $output .= '<p>Media URL: ' . esc_url($post['media_url']) . '</p>';
                    $output .= '<p>Thumbnail URL: ' . esc_url($post['thumbnail_url']) . '</p>';
                    $output .= '<p>Permalink: ' . esc_url($post['permalink']) . '</p>';
                    $output .= '<p>Timestamp: ' . esc_html($post['timestamp']) . '</p>';
                    $output .= '</div>'; */
					$output .='<div class="col-xl-3 col-md-6 col-12 soc-container">	<a href="'.esc_url($post['permalink']).'" target="_blank"
                            class="social-item m-insta social-overlay" style="background-image: url('.$media.'); ">
					<span class="social-text">'.$caption.'<span class="social-icon"><img src="'.get_stylesheet_directory_uri().'/img/icon-instagram.png" alt="Instagram Icon"></span></span>
					</a>

                        </div>';
                }
                $output .= '</div>';
				$output .= '<div class="col-12 social-column social-column-blue justify-content-center mt-5">';	
				if(!empty($fburl) && $fburl != '#'){	
				$output .= '<a href="'.$fburl.'" target="_blank" class="facebook-icon"
                        rel="noopener">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="ufl-brands ufl-facebook"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg>
				<span class="visually-hidden">Facebook Icon</span>
			</a>';
				}
		if(!empty($twitterurl) && $twitterurl != '#'){		
		$output .= '<a href="'.$twitterurl.'" target="_blank" class="twitter-icon"
                        rel="noopener">
				<svg viewBox="0 0 1200 1227" fill="none" xmlns="http://www.w3.org/2000/svg" class="ufl-brands ufl-twitter"><path d="M714.163 519.284L1160.89 0H1055.03L667.137 450.887L357.328 0H0L468.492 681.821L0 1226.37H105.866L515.491 750.218L842.672 1226.37H1200L714.137 519.284H714.163ZM569.165 687.828L521.697 619.934L144.011 79.6944H306.615L611.412 515.685L658.88 583.579L1055.08 1150.3H892.476L569.165 687.854V687.828Z" fill="currentColor"/></svg>
				<span class="visually-hidden">Twitter Icon</span>
			</a>';
		}
		if(!empty($instagramurl) && $instagramurl != '#'){		
		$output .='<a href="'.$instagramurl.'" target="_blank" class="instagram-icon"
                        rel="noopener">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="ufl-brands ufl-instagram"><path fill="currentColor"  d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
				<span class="visually-hidden">Instagram Icon</span>
			</a>';
	}
		if(!empty($youtubeurl) && $youtubeurl != '#'){		
		$output .='<a href="'.$youtubeurl.'" target="_blank"
                        class="youtube-icon" rel="noopener">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="ufl-brands ufl-youtube"><path fill="currentColor"  d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z"/></svg>
				<span class="visually-hidden">Youtube Icon</span>
			</a>';
		}

                    $output .='</div>';
				$output .= '</div></section>';
            } else {
                $output = '<p style="text-align: center;"> No Instagram posts found.</p>';
            }
        }
    } else {
        //$output = '<p>Please provide an access token to fetch Instagram posts.</p>';
		$metadata =  get_post_meta($page_id,'instagram_data',false);
		//var_dump($metadata);
		$data = json_decode($metadata[0], true);
			
            if (isset($data)) {
                $output .= '<section class="social-media-shell cdb"><div class="social-media-shell-wrapper"><p class="social-media-shell-title">'.$sectionheading.'</p><div class="social-feed row">';
                foreach ($data['data'] as $post) {
					// Check if the 'caption' key exists
                $caption = isset($post['caption']) ? esc_html($post['caption']) : '';
					if($post['media_type'] == 'IMAGE' || $post['media_type'] == 'CAROUSEL_ALBUM'){
					$media = esc_url($post['media_url']);
				}
				else{
					$media = esc_url($post['thumbnail_url']);
				}
                   /*  $output .= '<div>';
                    $output .= '<p>ID: ' . esc_html($post['id']) . '</p>';
                    $output .= '<p>Caption: ' . esc_html($post['caption']) . '</p>';
                    $output .= '<p>Media Type: ' . esc_html($post['media_type']) . '</p>';
                    $output .= '<p>Media URL: ' . esc_url($post['media_url']) . '</p>';
                    $output .= '<p>Thumbnail URL: ' . esc_url($post['thumbnail_url']) . '</p>';
                    $output .= '<p>Permalink: ' . esc_url($post['permalink']) . '</p>';
                    $output .= '<p>Timestamp: ' . esc_html($post['timestamp']) . '</p>';
                    $output .= '</div>'; */
					$output .='<div class="col-xl-3 col-md-6 col-12 soc-container">	<a href="'.esc_url($post['permalink']).'" target="_blank"
                            class="social-item m-insta social-overlay" style="background-image: url('.$media.'); ">
					<span class="social-text">'.$caption.'<span class="social-icon"><img src="'.get_stylesheet_directory_uri().'/img/icon-instagram.png" alt="Instagram Icon"></span></span>
					</a>

                        </div>';
                }
                $output .= '</div>';
				$output .= '<div class="col-12 social-column social-column-blue justify-content-center mt-5">';	
				if(!empty($fburl) && $fburl != '#'){	
				$output .= '<a href="'.$fburl.'" target="_blank" class="facebook-icon"
                        rel="noopener">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="ufl-brands ufl-facebook"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg>
				<span class="visually-hidden">Facebook Icon</span>
			</a>';
				}
		if(!empty($twitterurl) && $twitterurl != '#'){		
		$output .= '<a href="'.$twitterurl.'" target="_blank" class="twitter-icon"
                        rel="noopener">
				<svg viewBox="0 0 1200 1227" fill="none" xmlns="http://www.w3.org/2000/svg" class="ufl-brands ufl-twitter"><path d="M714.163 519.284L1160.89 0H1055.03L667.137 450.887L357.328 0H0L468.492 681.821L0 1226.37H105.866L515.491 750.218L842.672 1226.37H1200L714.137 519.284H714.163ZM569.165 687.828L521.697 619.934L144.011 79.6944H306.615L611.412 515.685L658.88 583.579L1055.08 1150.3H892.476L569.165 687.854V687.828Z" fill="currentColor"/></svg>
				<span class="visually-hidden">Twitter Icon</span>
			</a>';
		}
		if(!empty($instagramurl) && $instagramurl != '#'){		
		$output .='<a href="'.$instagramurl.'" target="_blank" class="instagram-icon"
                        rel="noopener">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="ufl-brands ufl-instagram"><path fill="currentColor"  d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
				<span class="visually-hidden">Instagram Icon</span>
			</a>';
	}
		if(!empty($youtubeurl) && $youtubeurl != '#'){		
		$output .='<a href="'.$youtubeurl.'" target="_blank"
                        class="youtube-icon" rel="noopener">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="ufl-brands ufl-youtube"><path fill="currentColor"  d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z"/></svg>
				<span class="visually-hidden">Youtube Icon</span>
			</a>';
		}

                    $output .='</div>';
				$output .= '</div></section>';
            } else {
                $output = '<p style="text-align: center;"> No Instagram posts found.</p>';
            }
    }

    return $output;
}

register_block_type('instagram-feed-block/instagram-block', array(
    'render_callback' => 'render_instagram_feed_block',
));
