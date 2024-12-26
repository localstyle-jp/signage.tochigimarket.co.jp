<?php
/**
 * ページネーションのレイアウトを変更したい場合はここを編集
 */
return [
    'nextActive' => '<li class="page-item" style="display: inline-block;"><a href="{{url}}" class="page-link">{{text}}</a></li>',
    'nextDisabled' => '<li class="page-item disabled" style="display: inline-block;"><a href="#">{{text}}</a></li>',
    'prevActive' => '<li class="page-item" style="display: inline-block;"><a href="{{url}}" class="page-link">{{text}}</a></li>',
    'prevDisabled' => '<li class="page-item disabled" style="display: inline-block;"><a href="#">{{text}}</a></li>',
    'counterRange' => '{{start}} - {{end}} of {{count}}',
    'counterPages' => '{{page}} of {{pages}}',
    'first' => '<li class="page-item" style="display: inline-block;"><a href="{{url}}" class="page-link">{{text}}</a></li>',
    'last' => '<li class="page-item" style="display: inline-block;"><a href="{{url}}" class="page-link">{{text}}</a></li>',
    'number' => '<li class="page-item" style="display: inline-block;"><a href="{{url}}" class="page-link">{{text}}</a></li>',
    'current' => '<li class="page-item active" style="display: inline-block;"><span class="page-link">{{text}}</span></li>',
    'ellipsis' => '<li class="ellipsis">&hellip;</li>',
    'sort' => '<a href="{{url}}">{{text}}</a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}}</a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}}</a>',
    'sortAscLocked' => '<a class="asc locked" href="{{url}}">{{text}}</a>',
    'sortDescLocked' => '<a class="desc locked" href="{{url}}">{{text}}</a>',
];
