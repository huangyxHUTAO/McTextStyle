# McTextStyle

一个 MediaWiki 扩展，允许在 Wiki 页面中使用 Minecraft 风格的文本格式化代码。

## 项目背景

这个 MediaWiki 扩展源自前端工具 [mc-text-formatter](https://github.com/EaseCation/mc-text-formatter)，该工具是一个 Vue.js 应用，用于实时预览和转换 Minecraft 格式代码喵！

## 功能特性

- 支持标准 Minecraft § 颜色代码（§a, §b, §c 等）
- 提供友好的 {} 别名语法（如 `{red}`, `{bold}`, `{green}`）
- 支持多种样式：粗体、斜体、下划线、删除线
- 包含扩展颜色（§g, §h, §i 等）
- 安全处理 HTML，防止 XSS 攻击
- 自动换行处理（`{enter}` 或 `\n` 转为 `<br>`）

## 安装方法

1. 将扩展放入 `extensions/McTextStyle/` 目录
2. 在 `LocalSettings.php` 中添加：
```php
wfLoadExtension( 'McTextStyle' );
```
3. 运行更新脚本：`php maintenance/update.php`

## 语法示例
```wikitext
{{#McTextStyle:§b南鸢晨星§r}}
{{#mctextstyle: {red}红色文本{reset} 普通文本}}
```