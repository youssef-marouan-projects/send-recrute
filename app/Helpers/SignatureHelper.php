<?php

// Builds a personalized HTML email signature from a Signature model row.
// Ported from send_multi_email_to_recruters' build_signature_html().
class SignatureHelper
{
    public static function build($sig, $hasImage = false)
    {
        if (!$sig) {
            return '';
        }

        $imageShape   = $sig['image_shape'] ?? 'circle';
        $imageSize    = (int) ($sig['image_size'] ?? 80);
        $layout       = $sig['layout'] ?? 'horizontal';
        $accentColor  = $sig['accent_color'] ?? '#3b82f6';
        $showIcons    = !empty($sig['show_icons']);
        $fontFamily   = $sig['font_family'] ?? 'Arial, Helvetica, sans-serif';
        $linksColumns = (int) ($sig['links_columns'] ?? 1);

        $borderRadius = $imageShape === 'circle' ? '50%' : ($imageShape === 'rounded' ? '12px' : '4px');

        $imgHtml = '';
        if ($hasImage) {
            $imgHtml = '<img src="cid:signature_photo" width="' . $imageSize . '" height="' . $imageSize . '" '
                . 'style="border-radius: ' . $borderRadius . '; object-fit: cover; display: block; border: 2px solid #e2e8f0;">';
        }

        $links = [];
        if (!empty($sig['email'])) {
            $icon = $showIcons ? '&#128231; ' : '';
            $links[] = $icon . '<a href="mailto:' . htmlspecialchars($sig['email']) . '" style="color:#334155; text-decoration:none;">' . htmlspecialchars($sig['email']) . '</a>';
        }
        if (!empty($sig['phone'])) {
            $icon = $showIcons ? '&#128241; ' : '';
            $links[] = $icon . htmlspecialchars($sig['phone']);
        }
        if (!empty($sig['linkedin'])) {
            $icon = $showIcons ? '&#128279; ' : '';
            $links[] = $icon . '<a href="' . htmlspecialchars($sig['linkedin']) . '" style="color:#0a66c2; text-decoration:none;" target="_blank">LinkedIn</a>';
        }
        if (!empty($sig['github'])) {
            $icon = $showIcons ? '&#128025; ' : '';
            $links[] = $icon . '<a href="' . htmlspecialchars($sig['github']) . '" style="color:#333; text-decoration:none;" target="_blank">GitHub</a>';
        }
        if (!empty($sig['portfolio'])) {
            $icon = $showIcons ? '&#127760; ' : '';
            $links[] = $icon . '<a href="' . htmlspecialchars($sig['portfolio']) . '" style="color:#0f766e; text-decoration:none;" target="_blank">Portfolio</a>';
        }

        if ($linksColumns <= 1 || count($links) <= 1) {
            $linksHtml = implode('<br>', $links);
        } else {
            $cols = min($linksColumns, count($links));
            $rows = [];
            for ($i = 0; $i < count($links); $i += $cols) {
                $chunk = array_slice($links, $i, $cols);
                $tds = '';
                foreach ($chunk as $link) {
                    $tds .= '<td style="padding-right:18px; padding-bottom:4px; white-space:nowrap;">' . $link . '</td>';
                }
                while (count($chunk) < $cols) {
                    $tds .= '<td></td>';
                    $chunk[] = '';
                }
                $rows[] = '<tr>' . $tds . '</tr>';
            }
            $linksHtml = '<table cellpadding="0" cellspacing="0" border="0" style="font-size:13px;">' . implode('', $rows) . '</table>';
        }

        $custom = '';
        if (!empty($sig['custom_text'])) {
            $custom = '<div style="margin-top:8px; color:#64748b; font-size:13px;">' . nl2br(htmlspecialchars($sig['custom_text'])) . '</div>';
        }

        $name  = htmlspecialchars($sig['name'] ?? '');
        $title = htmlspecialchars($sig['title'] ?? '');

        if ($layout === 'horizontal') {
            return '<br><br>
            <table cellpadding="0" cellspacing="0" border="0"
                   style="font-family: ' . $fontFamily . '; font-size: 14px; color: #1e293b;
                          border-top: 2px solid ' . $accentColor . '; padding-top: 14px; margin-top: 10px;">
                <tr>
                    <td style="padding-right: 16px; vertical-align: top; width: ' . ($imageSize + 20) . 'px;">' . $imgHtml . '</td>
                    <td style="vertical-align: top;">
                        <strong style="font-size: 16px; color: ' . $accentColor . ';">' . $name . '</strong><br>
                        <span style="color: #64748b; font-size: 13px;">' . $title . '</span><br><br>
                        ' . $linksHtml . $custom . '
                    </td>
                </tr>
            </table>';
        }

        return '<br><br>
        <table cellpadding="0" cellspacing="0" border="0"
               style="font-family: ' . $fontFamily . '; font-size: 14px; color: #1e293b;
                      border-top: 2px solid ' . $accentColor . '; padding-top: 14px; margin-top: 10px; text-align: center;">
            <tr><td style="padding-bottom: 12px;">' . $imgHtml . '</td></tr>
            <tr><td>
                <strong style="font-size: 16px; color: ' . $accentColor . ';">' . $name . '</strong><br>
                <span style="color: #64748b; font-size: 13px;">' . $title . '</span><br><br>
                ' . $linksHtml . $custom . '
            </td></tr>
        </table>';
    }
}
