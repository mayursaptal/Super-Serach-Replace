<h1>Super SearchReplace</h1>

<div style="max-width: 800px;margin:0 auto;">
    <div style="width: 100%;height:250px;padding:10px; color:#fff;background:#000;overflow-y: scroll;" id="ssr_container" class="ssr_container">
        <h2 style="font-size: 40px;  color: #fff !important;">Welcome!</h2>
        <p style="font-size: 16px;" class="ssr_status">
            This is an ultimate plugin which replaces Text from the complete WordPress installation which includes database as well as from file types PHP, CSS, JS, Text.
            <br> Before starting please verify the Searchand Replace text, Once started it can't be stopped until complete.
            <br>#~ start
        </p>
    </div>
    <br>
    <form id="ssr_form" onsubmit="return false;">
        <div style="display: flex;    justify-content: space-between;">
            <input required type="text" class="regular-text code" id="ssr_search" placeholder="Search">
            <input required type="text" class="regular-text code" id="ssr_replace" placeholder="Replace">
            <button id="ssr_form_btn" type="submit" class="replace button button-primary">Start</button>
        </div>
    </form>
</div>