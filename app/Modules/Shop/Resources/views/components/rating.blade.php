<div class="rating-row">
    <div class="rating" title="Рейтинг: {{$rating/10}}"><span style="width: {{ceil($rating / 5*6)}}px"></span></div>
    <span class="value">{{ number_format($rating/10, 1, '.', ' ') }}</span>
</div>
