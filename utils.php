<?php
namespace McTextStyle;   // 统一小空间，方便引用

class Utils
{
    /**
     * 将 Minecraft 格式文本转成 HTML
     * @param string $raw        原始文本
     * @param bool   $braceMode  是否先展开 {} 语法
     * @return string            带内联 style 的 HTML 片段
     */
    public static function mcTextToHtml(string $raw, bool $braceMode = true): string
    {
        /* ====== 颜色映射 ====== */
        $colors = [
            '§0' => '#000000',
            '§1' => '#0000AA',
            '§2' => '#00AA00',
            '§3' => '#00AAAA',
            '§4' => '#AA0000',
            '§5' => '#AA00AA',
            '§6' => '#FFAA00',
            '§7' => '#AAAAAA',
            '§8' => '#555555',
            '§9' => '#5555FF',
            '§a' => '#55FF55',
            '§b' => '#55FFFF',
            '§c' => '#FF5555',
            '§d' => '#FF55FF',
            '§e' => '#FFFF55',
            '§f' => '#FFFFFF',
            '§g' => '#DDD605',
            '§h' => '#E3D4D1',
            '§i' => '#CECACA',
            '§j' => '#443A3B',
            '§m' => '#971607',
            '§n' => '#B4684D',
            '§p' => '#DEB12D',
            '§q' => '#11A036',
            '§s' => '#2CBAA8',
            '§t' => '#21497B',
            '§u' => '#9A5CC6'
        ];

        /* ====== {} 展开表 ====== */
        $braceMap = [
            '{black}'       => '§0',
            '{dark-blue}'   => '§1',
            '{dark-green}'  => '§2',
            '{dark-aqua}'   => '§3',
            '{dark-red}'    => '§4',
            '{dark-purple}' => '§5',
            '{gold}'        => '§6',
            '{gray}'        => '§7',
            '{dark-gray}'   => '§8',
            '{blue}'        => '§9',
            '{green}'       => '§a',
            '{aqua}'        => '§b',
            '{red}'         => '§c',
            '{light-purple}'=> '§d',
            '{pink}'        => '§d',
            '{yellow}'      => '§e',
            '{white}'       => '§f',
            '{reset}'       => '§r',
            '{bold}'        => '§l',
            '{italic}'      => '§o',
            '{enter}'       => "\n"
        ];

        $text = $raw;
        if ($braceMode) {
            $text = strtr($text, $braceMap);
        }

        $state  = ['color'=>'', 'bold'=>false, 'italic'=>false, 'underline'=>false, 'strikethrough'=>false];
        $buffer = '';
        $html   = '';

        $flush = function() use (&$state, &$buffer, &$html) {
            if ($buffer === '') { return; }
            $style = "color:{$state['color']};";
            if ($state['bold'])          $style .= 'font-weight:bold;';
            if ($state['italic'])        $style .= 'font-style:italic;';
            if ($state['underline'])     $style .= 'text-decoration:underline;';
            if ($state['strikethrough']) $style .= 'text-decoration:line-through;';
            $html .= "<span style=\"{$style}\">" . $buffer . "</span>";
            $buffer = '';
        };

        $len = mb_strlen($text, 'UTF-8');
        for ($i = 0; $i < $len; $i++) {
            $ch = mb_substr($text, $i, 1, 'UTF-8');
            if ($ch === '§' && $i + 1 < $len) {
                $code = mb_substr($text, ++$i, 1, 'UTF-8');
                $flush();
                switch ($code) {
                    case 'r':
                        $state = ['color'=>'', 'bold'=>false, 'italic'=>false, 'underline'=>false, 'strikethrough'=>false];
                        break;
                    case 'l': $state['bold']          = true; break;
                    case 'o': $state['italic']        = true; break;
                    case 'n': $state['underline']     = true; break;
                    case 'm': $state['strikethrough'] = true; break;
                    default:
                        $state['color'] = $colors["§{$code}"] ?? $state['color'];
                }
            } elseif ($ch === "\n") {
                $flush();
                $html .= '<br>';
            } else {
                $buffer .= $ch;
            }
        }
        $flush();
        return $html;
    }
}