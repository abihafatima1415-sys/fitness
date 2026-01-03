<?php 
function generateCaptcha() {
    $captcha = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 5);
    $_SESSION['login_captcha'] = $captcha;

    header('Content-Type: image/svg+xml');

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="40">';
    $svg .= '<rect width="100%" height="100%" fill="#f2f2f2"/>';

    // Noise lines
    for ($i = 0; $i < 5; $i++) {
        $svg .= '<line x1="'.rand(0,120).'" y1="'.rand(0,40).'"
                        x2="'.rand(0,120).'" y2="'.rand(0,40).'"
                        stroke="#ccc"/>';
    }

    // Text
    $svg .= '<text x="15" y="28"
                font-size="20"
                font-family="monospace"
                fill="#333"
                letter-spacing="3">'.$captcha.'</text>';

    $svg .= '</svg>';

    echo $svg;
    exit;
}
generateCaptcha();
?>