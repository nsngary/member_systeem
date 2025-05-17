<?php
/**
 * Flash / Toast 公用函式
 * set_flash('success','內容')  或  set_flash('error','內容')
 * 任一頁 <head> 後呼叫 flash_js()，JS 會自動產生 Toast
 */
function set_flash(string $type, string $msg): void
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function pop_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);    // 取一次即銷毀
    return $f;
}

/** 將 Flash 內容輸出為前端可讀取的全域變數 */
function flash_js(): void
{
    if ($f = pop_flash()) {
        $json = json_encode($f, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        echo "<script>window.__FLASH = $json;</script>";
    }
}
