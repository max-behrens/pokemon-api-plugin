<?php

class ApiShortcode {
    
    public function __construct() {
        add_shortcode('pokemon_api_data', array($this, 'render_shortcode'));
    }
    
    public function render_shortcode($atts) {
        // Parse shortcode attributes.
        $atts = shortcode_atts(array(
            'type' => 'pokemon',
            'limit' => 12
        ), $atts);
        
        $api_handler = new ApiHandler();
        
        // Show loading state.
        ob_start();
        echo '<div id="api-loading">Loading data...</div>';
        
        $data = $api_handler->fetch_pokemon_data($atts['limit']);
        $output = $this->render_pokemon($data);
        
        if (empty($data)) {
            return '<div class="api-error">Failed to load data. Please try again later.</div>';
        }
        
        echo '<script>document.getElementById("api-loading").style.display = "none";</script>';
        echo $output;
        
        return ob_get_clean();
    }
    
    private function render_pokemon($pokemon_data) {
        if (empty($pokemon_data)) {
            return '<div class="api-error">No Pokemon data available.</div>';
        }
        
        $output = '<div class="api-grid pokemon-grid">';
        
        foreach ($pokemon_data as $pokemon) {
            $output .= '<div class="api-card pokemon-card">';
            $output .= '<div class="card-image">';
            $output .= '<img src="' . esc_url($pokemon['image']) . '" alt="' . esc_attr($pokemon['name']) . '" loading="lazy">';
            $output .= '</div>';
            $output .= '<div class="card-content">';
            $output .= '<h3>' . esc_html($pokemon['name']) . '</h3>';
            $output .= '<p><strong>Type:</strong> ' . esc_html($pokemon['types']) . '</p>';
            $output .= '<p><strong>Height:</strong> ' . esc_html($pokemon['height']) . ' dm</p>';
            $output .= '<p><strong>Weight:</strong> ' . esc_html($pokemon['weight']) . ' hg</p>';
            $output .= '</div>';
            $output .= '</div>';
        }
        
        $output .= '</div>';
        return $output;
    }
}