<?php

class ApiHandler {
    
    private $cache_key = 'pokemon_api_cache';
    private $cache_expiration = 3600; // 1 hour until API is called again.
    
    
    /**
     * Fetch data from PokeAPI
     */
    public function fetch_pokemon_data($limit = 12) {

        // Check cache first.
        $cached_data = get_transient($this->cache_key);
        if ($cached_data !== false) {
            return $cached_data;
        }
        
        $pokemon_list = $this->get_pokemon_list($limit);
        $detailed_pokemon = array();
        
        foreach ($pokemon_list as $pokemon) {
            $details = $this->get_pokemon_details($pokemon['url']);
            if ($details) {
                $detailed_pokemon[] = array(
                    'name' => ucfirst($details['name']),
                    'image' => $details['sprites']['other']['official-artwork']['front_default'] ?? $details['sprites']['front_default'],
                    'types' => implode(', ', array_column($details['types'], 'type')['name'] ?? []),
                    'height' => $details['height'],
                    'weight' => $details['weight'],
                    'id' => $details['id']
                );
            }
        }
        
        // Cache the results.
        set_transient($this->cache_key, $detailed_pokemon, $this->cache_expiration);
        
        return $detailed_pokemon;
    }
    
    private function get_pokemon_list($limit) {
        $response = wp_remote_get("https://pokeapi.co/api/v2/pokemon/?limit={$limit}");
        
        if (is_wp_error($response)) {
            return array();
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        return $data['results'] ?? array();
    }
    
    private function get_pokemon_details($url) {
        $response = wp_remote_get($url);
        
        if (is_wp_error($response)) {
            return null;
        }
        
        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true);
    }
}