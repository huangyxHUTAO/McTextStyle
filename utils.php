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
            // 基础色
            '§0' => ['fg' => '#000000', 'bg' => '#000000'], // black
            '§1' => ['fg' => '#0000AA', 'bg' => '#00002A'], // dark_blue
            '§2' => ['fg' => '#00AA00', 'bg' => '#002A00'], // dark_green
            '§3' => ['fg' => '#00AAAA', 'bg' => '#002A2A'], // dark_aqua
            '§4' => ['fg' => '#AA0000', 'bg' => '#2A0000'], // dark_red
            '§5' => ['fg' => '#AA00AA', 'bg' => '#2A002A'], // dark_purple
            '§6' => ['fg' => '#FFAA00', 'bg' => '#402A00'], // gold
            '§7' => ['fg' => '#AAAAAA', 'bg' => '#2A2A2A'], // gray
            '§8' => ['fg' => '#555555', 'bg' => '#151515'], // dark_gray
            '§9' => ['fg' => '#5555FF', 'bg' => '#15153F'], // blue
            '§a' => ['fg' => '#55FF55', 'bg' => '#153F15'], // green
            '§b' => ['fg' => '#55FFFF', 'bg' => '#153F3F'], // aqua
            '§c' => ['fg' => '#FF5555', 'bg' => '#3F1515'], // red
            '§d' => ['fg' => '#FF55FF', 'bg' => '#3F153F'], // light_purple
            '§e' => ['fg' => '#FFFF55', 'bg' => '#3F3F15'], // yellow
            '§f' => ['fg' => '#FFFFFF', 'bg' => '#3F3F3F'], // white

            // 基岩版扩展颜色
            '§g' => ['fg' => '#DDD605', 'bg' => '#373501'], // minecoin_gold
            '§h' => ['fg' => '#E3D4D1', 'bg' => '#383534'], // material_quartz
            '§i' => ['fg' => '#CECACA', 'bg' => '#333232'], // material_iron
            '§j' => ['fg' => '#443A3B', 'bg' => '#110E0E'], // material_netherite
            '§m' => ['fg' => '#971607', 'bg' => '#250501'], // material_redstone
            '§n' => ['fg' => '#B4684D', 'bg' => '#2D1A13'], // material_copper
            '§p' => ['fg' => '#DEB12D', 'bg' => '#372C0B'], // material_gold
            '§q' => ['fg' => '#47A036', 'bg' => '#04280D'], // material_emerald
            '§s' => ['fg' => '#2CBAA8', 'bg' => '#0B2E2A'], // material_diamond
            '§t' => ['fg' => '#21497B', 'bg' => '#08121E'], // material_lapis
            '§u' => ['fg' => '#9A5CC6', 'bg' => '#261731'], // material_amethyst
            '§v' => ['fg' => '#EB7114', 'bg' => '#3B1D05'], // material_resin
        ];

        /* ====== {} 展开表 ====== */
        $braceMap = [
            '{black}' => '§0',
            '{dark-blue}' => '§1',
            '{dark-green}' => '§2',
            '{dark-aqua}' => '§3',
            '{dark-red}' => '§4',
            '{dark-purple}' => '§5',
            '{gold}' => '§6',
            '{gray}' => '§7',
            '{dark-gray}' => '§8',
            '{blue}' => '§9',
            '{green}' => '§a',
            '{aqua}' => '§b',
            '{red}' => '§c',
            '{light-purple}' => '§d',
            '{pink}' => '§d',
            '{yellow}' => '§e',
            '{white}' => '§f',
            '{reset}' => '§r',
            '{bold}' => '§l',
            '{italic}' => '§o',
            '{underline}' => '§n',
            '{strikethrough}' => '§m',
            '{enter}' => "\n"
        ];

        $text = $raw;
        if ($braceMode) {
            $text = strtr($text, $braceMap);
        }

        $state = [
            'color' => '',           // 前景色
            'bg' => '',              // 阴影色
            'bold' => false,         // 是否加粗
            'italic' => false,       // 是否斜体
            'underline' => false,    // 是否下划线
            'strikethrough' => false // 是否删除线
        ];
        $buffer = '';
        $html = '';

        $flush = function () use (&$state, &$buffer, &$html) {
            if ($buffer === '') {
                return;
            }

            $styles = ["color:{$state['color']}"];

            // 注入 CSS 变量供 text-shadow 使用
            if ($state['bg']) {
                $styles[] = "--shadow-color:{$state['bg']}";
            }

            if ($state['bold'])
                $styles[] = 'font-weight:bold';
            if ($state['italic'])
                $styles[] = 'font-style:italic';
            if ($state['underline'])
                $styles[] = 'text-decoration:underline';
            if ($state['strikethrough'])
                $styles[] = 'text-decoration:line-through';

            // 处理同时存在 underline 和 strikethrough 的情况
            if ($state['underline'] && $state['strikethrough']) {
                $styles[] = 'text-decoration:underline line-through';
            }

            $styleStr = implode(';', $styles);
            $html .= "<span style=\"{$styleStr}\">{$buffer}</span>";
            $buffer = '';
        };

        $len = mb_strlen($text, 'UTF-8');
        for ($i = 0; $i < $len; $i++) {
            $ch = mb_substr($text, $i, 1, 'UTF-8');

            if ($ch === '§' && $i + 1 < $len) {
                $code = mb_substr($text, ++$i, 1, 'UTF-8');
                $flush();

                switch ($code) {
                    case 'r': // reset
                        $state = [
                            'color' => '',
                            'bg' => '',
                            'bold' => false,
                            'italic' => false,
                            'underline' => false,
                            'strikethrough' => false
                        ];
                        break;
                    case 'l':
                        $state['bold'] = true;
                        break;
                    case 'o':
                        $state['italic'] = true;
                        break;
                    case 'n':
                        $state['underline'] = true;
                        break;
                    case 'm':
                        $state['strikethrough'] = true;
                        break;
                    default:
                        if (isset($colors["§{$code}"])) {
                            $state['color'] = $colors["§{$code}"]['fg'];
                            $state['bg'] = $colors["§{$code}"]['bg'];
                        }
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