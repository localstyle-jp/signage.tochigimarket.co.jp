<?php
/**
 * ページネーションのレイアウトを変更したい場合はここを編集
 */
return [
    'nextActive' => '<a href="{{url}}" class="btn btn-secondary text-light">{{text}}</a>',
    'nextDisabled' => '<li class="next disabled"><a href="" onclick="return false;">{{text}}</a></li>',
    'prevActive' => '<a href="{{url}}" class="btn btn-secondary text-light">{{text}}</a>',
    'prevDisabled' => '<li class="prev disabled"><a href="" onclick="return false;">{{text}}</a></li>',
    'counterRange' => '{{start}} - {{end}} of {{count}}',
    'counterPages' => '{{page}} of {{pages}}',
    'first' => '<a href="{{url}}" class="btn btn-secondary text-light">{{text}}</a>',
    'last' => '<a href="{{url}}" class="btn btn-secondary text-light">{{text}}</a>',
    'number' => '<a href="{{url}}" class="btn btn-secondary text-light">{{text}}</a>',
    'current' => '<a href="" class="btn btn-warning text-dark">{{text}}</a>',
    'ellipsis' => '<li class="ellipsis">&hellip;</li>',
    'sort' => '<a href="{{url}}">{{text}}</a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}}</a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}}</a>',
    'sortAscLocked' => '<a class="asc locked" href="{{url}}">{{text}}</a>',
    'sortDescLocked' => '<a class="desc locked" href="{{url}}">{{text}}</a>',
];