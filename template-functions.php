<?php

if ( !function_exists('print_lang_value') ) {
    function print_lang_value($value, $lang_code, $echo=true){
        $lang_code = substr($lang_code,0,2);
        
        if ( is_array($value) ) {
            foreach ($value as $current_value) {
                $print_values[] = get_lang_value($current_value, $lang_code);
            }
            
            $text = implode(', ', $print_values);
        } else {
            $text = get_lang_value($value, $lang_code);
        }

        if ( $echo ) {
            echo $text;
        } else {
            return $text;
        }
    }
}

if ( !function_exists('get_lang_value') ) {
    function get_lang_value($string, $lang_code, $default_lang_code='en'){
        $lang_value = array();
        $occs = preg_split('/\|/', $string);

        foreach ($occs as $occ){
            $re_sep = (strpos($occ, '~') !== false ? '/\~/' : '/\^/');
            $lv = preg_split($re_sep, $occ);
            $lang = substr($lv[0],0,2);
            $value = $lv[1];
            $lang_value[$lang] = $value;
        }

        if ( isset($lang_value[$lang_code]) ) {
            $translated = $lang_value[$lang_code];
        } elseif ( isset($lang_value[$default_lang_code]) ) {
            $translated = $lang_value[$default_lang_code];
        } else {
            $translated = array_values($lang_value)[0];
        }

        return $translated;
    }
}

if ( !function_exists('format_date') ) {
    function format_date($string){
        $date_formated = '';

        if (strpos($string,'-') !== false) {
            $date_formated = substr($string,8,2)  . '/' . substr($string,5,2) . '/' . substr($string,0,4);
        }else{
            $date_formated =  substr($string,6,2)  . '/' . substr($string,4,2) . '/' . substr($string,0,4);
        }

        return $date_formated;
    }
}

if ( !function_exists('format_act_date') ) {
    function format_act_date($string, $lang){
        $months = array();
        $months['pt'] = array('Janeiro','Feveiro', 'Março', 'Abril', 'Maio', 'Junho',
                              'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

        $months['es'] = array('Enero','Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                              'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

        $date_formated = '';
        if (strpos($string,'-') !== false) {
            if ($lang != 'en'){
                $month_val = intval(substr($string,5,2));
                $month_name = $months[$lang][$month_val-1];
            } else {
                $month_name = strftime("%B", strtotime($string));
            }
            $date_formated = substr($string,8,2) . ' ' . __('of','e-blueinfo') . ' ' . $month_name . ' ' . __('of', 'e-blueinfo') . ' ' . substr($string,0,4);
        }else{
            $date_formated =  substr($string,6,2)  . '/' . substr($string,4,2) . '/' . substr($string,0,4);
        }

        return $date_formated;
    }
}

if ( !function_exists('isUTF8') ) {
    function isUTF8($string){
        return (utf8_encode(utf8_decode($string)) == $string);
    }
}

if ( !function_exists('translate_label') ) {
    function translate_label($texts, $label, $group=NULL) {
        // labels on texts.ini must be array key without spaces
        $label_norm = preg_replace('/[&,\'\s]+/', '_', $label);

        if($group == NULL) {
            if(isset($texts[$label_norm]) and $texts[$label_norm] != "") {
                return $texts[$label_norm];
            }
        } else {
            if(isset($texts[$group][$label_norm]) and $texts[$group][$label_norm] != "") {
                return $texts[$group][$label_norm];
            }
        }

        // case translation not found return original label ucfirst
        return ucfirst($label);
    }
}

if ( !function_exists('get_site_meta_tags') ) {
    function get_site_meta_tags($url){
        $site_title = array();
        $fp = @file_get_contents($url);

        if ($fp) {
            $res = preg_match("/<title>(.*)<\/title>/siU", $fp, $title_matches);
            if ($res) {
                $site_title = preg_replace('/\s+/', ' ', $title_matches[1]);
                $site_title = trim($site_title);
            }

            $site_meta_tags = get_meta_tags($url);
            $site_meta_tags['title'] = $site_title;

            foreach ($site_meta_tags as $key => $value) {
                if (!isUTF8($value)){
                    $site_meta_tags[$key] = utf8_encode($value);
                }
            }
        }

        return $site_meta_tags;
    }
}

if ( !function_exists('real_site_url') ) {
    function real_site_url($path = ''){
        $site_url = get_site_url();

        // check for multi-language-framework plugin
        if ( function_exists('mlf_parseURL') ) {
            global $mlf_config;
            $current_language = substr( strtolower(get_bloginfo('language')),0,2 );

            if ( $mlf_config['default_language'] != $current_language ){
                $site_url .= '/' . $current_language;
            }
        }
        // check for polylang plugin
        elseif ( defined( 'POLYLANG_VERSION' ) ) {
            $default_language = pll_default_language();
            $current_language = pll_current_language();

            if ( $default_language != $current_language ){
                $site_url .= '/' . $current_language;
            }
        }

        if ($path != ''){
            $site_url .= '/' . $path;
        }

        $site_url .= '/';

        return $site_url;
    }
}

if ( !function_exists('short_string') ) {
    function short_string($string, $len=400){
        if ( strlen($string) > $len ) {
            $string = mb_substr($string, 0, $len) . "...";
        }

        return $string;
    }
}

if ( !function_exists('get_highlight') ) {
    function get_highlight($snippets){
        $pattern = '/\.(?=\.{3})|\G(?!^)\./'; // remove dots from snippets

        if ( count($snippets) > 1 ) {
            $replace = preg_replace($pattern, '', end($snippets));
            $text = '...' . trim($replace) . '...';
        } else {
            $replace = preg_replace($pattern, '', $snippets[0]);
            $text = '...' . trim($replace) . '...';
        }

        return $text;
    }
}

if ( !function_exists('get_country_name') ) {
    function get_country_name($names, $lang){
        $country_name = '';

        if ( $names ) {
            foreach ($names as $name) {
                if (strpos($name, $lang) === 0) {
                    $arr = explode('^', $name);
                    $country_name = $arr[1];
                }
            }
        }

        return $country_name;
    }
}

if ( !function_exists('normalize_country_object') ) {
    function normalize_country_object($object, $lang){
        $obj = array();
        $_unset = array();

        if ( $object ) {
            $ids = wp_list_pluck( $object, 'id' );
            $names = wp_list_pluck( $object, 'name' );
            $obj = array_combine($ids, $names);

            foreach ($obj as $key => $value) {
                $labels = '';

                foreach ($value as $k => $v) {
                    if (strpos($v, $lang) === 0) {
                        $arr = explode('^', $v);
                        $labels = $arr[1];
                    }
                }

                $obj[$key] = $labels;
            }
        }

        if ( $_unset ) {
            foreach ($_unset as $key => $value) {
                unset($obj[$value]);
            }
        }

        asort($obj);

        return $obj;
    }
}

if ( !function_exists('remove_prefix') ) {
    function remove_prefix($name){
        $name = explode(' ', $name);
        $prefix = array_shift($name);
        $name = implode(' ', $name);

        return $name;
    }
}

if ( !function_exists('get_abstract') ) {
    function get_abstract($data, $lang){
        $bool = false;
        $abstract = '-';

        if ( $data ) {
            if ( count($data) > 1 ) {
                foreach ($data as $key => $value) {
                    $ab = explode(' ', $value);
                    $prefix = array_shift($ab);
                    $prefix = ltrim($prefix, '(');
                    $prefix = rtrim($prefix, ')');

                    if ( $prefix == $lang ) {
                        $abstract = $value;
                        $bool = true;
                        break;
                    }
                }

                if ( !$bool ) {
                    $abstract = $data[0];
                    // $abstract = remove_prefix($data[0]);
                }
            } else {
                $abstract = $data[0];
                // $abstract = remove_prefix($data[0]);
            }
        }

        return $abstract;
    }
}

if ( !function_exists('cmp') ) {
    function cmp($a, $b) {
        return strcmp($a->name, $b->name);
    }
}

if ( !function_exists('prepare_query') ) {
    function prepare_query($q){
        $query = '(mh:(QUERY)^50 OR ti:(QUERY)^30 OR ab:(QUERY)^10 OR _text_:(QUERY))';
        $query = str_replace('QUERY', $q, $query);

        return $query;
    }
}

if ( !function_exists('is_webview') ) {
    function is_webview() {
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $wv = strpos($userAgent, 'wv');
        $safari = strpos($userAgent, 'safari');
        $ios = preg_match('/iphone|ipod|ipad|macintosh/', $userAgent);

        if ( $ios ) {
            if ( $safari !== false ) {
                return false;
            } else {
                return true;
            }
        } else {
            if ( $wv !== false ) {
                return true;
            } else {
                return false;
            }
        }
    }
}

if ( !function_exists('is_timestamp') ) {
    function is_timestamp($timestamp) {
        return ((string) (int) $timestamp === $timestamp) 
            && ($timestamp <= PHP_INT_MAX)
            && ($timestamp >= ~PHP_INT_MAX);
    }
}

if ( !function_exists('get_leisref_title') ) {
    function get_leisref_title($doc, $lang) {
        if ( $doc->ti ) {
            $title = $doc->ti[0];
        } else {
            $act_type = print_lang_value($doc->at, $lang, false);
            $title = $act_type.' Nº '.$doc->an[0];
        }

        return $title;
    }
}

if ( !function_exists('get_leisref_fulltext') ) {
    function get_leisref_fulltext($doc, $lang) {
        $fulltext = array_filter($doc->fulltext, function($ft) use ($lang) {
            return strpos($ft, $lang) === 0;
        });

        if ( $fulltext ) {
            $document_url_parts = explode("|", $fulltext);
            $fulltext = $document_url_parts[1];
        } else {
            $document_url_parts = explode("|", $doc->fulltext[0]);
            $fulltext = $document_url_parts[1];
        }

        return $fulltext;
    }
}

if ( !function_exists('get_thumbnail') ) {
    function get_thumbnail($docid, $media) {
        global $thumb_service_url;
        $img = $thumb_service_url . '/' . $docid . '/' . $docid . '.jpg';
        $headers = @get_headers($img);
        $img_exists = strpos($headers[0],"200 OK") ? true : false;
        $img_dir = EBLUEINFO_PLUGIN_URL . 'template/images';

        $media_type = array(
            'pdf'   => $img_dir.'/thumbPDF.jpg',
            'video' => $img_dir.'/thumbVideo.jpg',
            'audio' => $img_dir.'/thumbAudio.jpg',
            'presentation' => $img_dir.'/thumbPPT.jpg',
            'image' => $img_dir.'/thumbImage.jpg',
            'link'  => $img_dir.'/thumbLink.jpg'
        );
        
        if ( $img_exists ) {
            $thumb = $img;
        } else {
            if ( array_key_exists($media, $media_type) )
                $thumb = $media_type[$media];
            else
                $thumb = $img_dir.'/nothumb.jpg';
        }

        return $thumb;
    }
}

if ( !function_exists('slugify') ) {
    function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}

if ( !function_exists('get_cluster') ) {
    function get_cluster($data){
        $odd = array();
        $even = array();
        $both = array(&$even, &$odd);
        array_walk($data, function($v, $k) use ($both) { $both[$k % 2][] = $v; });

        $i = 0;
        $cluster = array();
        foreach ($even as $key => $val) {
            $explode = explode('|', $val, 2);
            $cluster['_'.$explode[0]]['name'] = ( $explode[1] ) ? $explode[1] : $explode[0];
            $cluster['_'.$explode[0]]['total'] = $odd[$i];
            $i++;
        };

        return $cluster;
    }
}

if ( !function_exists('get_video_data') ) {
    function get_video_data($url) {
        $args = array();
        
        if (strpos($url, 'youtube.com') !== false) {
            $parts = parse_url($url);
            parse_str($parts['query'], $query);
            $args['embed'] = "https://www.youtube.com/embed/" . $query['v'];
            $args['type']  = 'youtube';
            $args['id']    = $query['v'];
        }

        if (strpos($url, 'youtu.be') !== false) {
            $parts = parse_url($url);
            $code = end(explode('/', $parts['path']));
            $args['embed'] = "https://www.youtube.com/embed/" . $code;
            $args['type']  = 'youtube';
            $args['id']    = $code;
        }

        return $args;
    }
}

if ( !function_exists('display_multimedia') ) {
    function display_multimedia($link, $docid, $media_type){
        $output = array();
        $link_data = parse_url($link);
        $ext = pathinfo($link, PATHINFO_EXTENSION);
        $img_ext = array('jpg', 'jpeg', 'png', 'gif');

        if (strpos($link_data['host'],'youtube.com') !== false) {
            $service = 'youtube';
            parse_str($link_data['query'], $params);
            $video_id = $params['v'];
        } elseif (strpos($link_data['host'],'youtu.be') !== false) {
            $service = 'youtube';
            $video_id = end(explode('/', $link_data['path']));
        } elseif (strpos($link_data['host'],'vimeo.com') !== false) {
            $service = 'vimeo';
            $video_id = $link_data['path'];
        } elseif (strpos($link_data['host'],'flickr.com') !== false) {
            $service = 'flicker';
        } elseif (strpos($link_data['host'],'slideshare.net') !== false) {
            $service = 'slideshare';
        } else {
            $service = false;
        }

        $output['service'] = $service;

        if ($service == 'youtube') {
            $output['html'] = '<div class="video-container"><iframe src="//www.youtube.com/embed/' . $video_id . '" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe></div>';
        } elseif ($service == 'vimeo') {
            $output['html'] = '<div class="video-container"><iframe src="//player.vimeo.com/video' . $video_id . '" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe></div>';
        } elseif ($service == 'flicker') {
            $output['html'] = '<div class="video-container"><iframe src="' . $link . '/player/" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe></div>';
        } elseif ($service == 'slideshare') {
            $embed_service_url = 'https://www.slideshare.net/api/oembed/2?url=' . $link . '&format=json';
            $embed_service_response = file_get_contents($embed_service_url);
            $embed_service_data = json_decode($embed_service_response, true);
            $output['html'] = '<div class="video-container">' . $embed_service_data['html'] . '</div>';
        } elseif ( in_array(strtolower($ext), $img_ext) ) {
            $output['html'] = '<img class="thumbnail-doc responsive-img" src="' . $link . '" alt=""></img>';
        } else {
            $output['html'] = '<img class="thumbnail-doc responsive-img" src="' . get_thumbnail($docid, $media_type) . '" alt="">';
        }

        return $output;
    }
}

if ( !function_exists('get_multimedia_parent_name') ) {
    function get_multimedia_parent_name($text, $lang) {
        $name = $text;
        $parent_name = explode('|', $name);
        $lang = ( 'pt' == $lang ) ? 'pt-br' : $lang;

        foreach ($parent_name as $pname) {
            $prefix = '('.$lang.')';

            if (substr($pname, 0, strlen($prefix)) === $prefix) {
                $name = trim(substr($pname, strlen($prefix)));
                break;
            }
        }

        return $name;
    }
}

?>
