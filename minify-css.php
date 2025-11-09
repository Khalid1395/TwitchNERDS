<?php
/**
 * Script de minification CSS simple
 * Utilisation: php minify-css.php
 */

function minifyCSS($css) {
    // Supprimer les commentaires
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    
    // Supprimer les espaces inutiles
    $css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
    $css = preg_replace('/\s+/', ' ', $css);
    
    // Supprimer les espaces autour de certains caractères
    $css = str_replace([' {', '{ ', ' }', '} ', ' :', ': ', ' ;', '; ', ' ,', ', ', ' >', '> ', ' +', '+ ', ' ~', '~ '], ['{', '{', '}', '}', ':', ':', ';', ';', ',', ',', '>', '>', '+', '+', '~', '~'], $css);
    
    // Supprimer les espaces en début et fin
    $css = trim($css);
    
    return $css;
}

// Minifier styles.css
if (file_exists('styles.css')) {
    $css = file_get_contents('styles.css');
    $minified = minifyCSS($css);
    file_put_contents('styles.min.css', $minified);
    echo "✅ styles.min.css créé avec succès\n";
    echo "Taille originale: " . number_format(strlen($css)) . " octets\n";
    echo "Taille minifiée: " . number_format(strlen($minified)) . " octets\n";
    echo "Réduction: " . number_format((1 - strlen($minified) / strlen($css)) * 100, 2) . "%\n";
} else {
    echo "❌ styles.css introuvable\n";
}

// Minifier styles-administration.css
if (file_exists('styles-administration.css')) {
    $css = file_get_contents('styles-administration.css');
    $minified = minifyCSS($css);
    file_put_contents('styles-administration.min.css', $minified);
    echo "✅ styles-administration.min.css créé avec succès\n";
}
?>

