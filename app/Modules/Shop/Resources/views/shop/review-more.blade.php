@foreach($reviews as $r)
    <li>
        <div class="profile">
            <div class="profile-photo">
                <img class="profile" src="{{ $r['reviewer']['avatar'] ?? '' }}"  />
                @isset($r['reviewer']['countryFlag'])
                    <img class="country" src="{{ $r['reviewer']['countryFlag'] }}" />
                @endisset
            </div>
            <div>
                @if (isset($r['grade']) && isset($r['reviewer']['name']) && isset($r['date']))
                    <x-shop::rating :rating="$r['grade']*10" />
                    <span class="user-name">{{ $r['reviewer']['name'] }}</span>
                    <span class="date">{{ $r['date']->format('d.m.Y') }}</span>
                @endif
            </div>
        </div>
        @isset($r['text'])
            <span class="text">{{ $r['text'] }}</span>
        @endisset

        <div class="img-list">
            @isset($r['images'])
                @foreach ($r['images'] as $img)
                    <a class="reviewImg" rel="review-img" target="_blank" href="{{ $img['url'] }}"><img class="img" src="{{ $img['url'] }}_100x100.jpg" /></a>
                @endforeach
            @endisset
        </div>
    </li>
@endforeach
