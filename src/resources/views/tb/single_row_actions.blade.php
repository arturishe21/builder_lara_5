<td style="width: 80px">
    @if(!empty($def->getActions()))
        <div style="display: inline-block">
            <div class="btn-group  pull-right">
                <a class="btn dropdown-toggle btn-default" data-toggle="dropdown">
                    <i class="fa fa-cog"></i> <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu">
                    @if($def->hasAction('custom'))
                        @foreach($def->getActions()['custom'] as $button)
                            {!! $actions->fetch('custom', $row, $button) !!}
                        @endforeach
                    @endif

                    @foreach($def->getActions() as $actionName => $actionArray)
                        @if($actionName == 'insert' || $actionName == 'filter' || $actionName == 'custom' ||
                         (isset($actionArray['check']) && !$actionArray['check']()))
                            @continue
                        @endif
                        {!! $actions->fetch($actionName, $row) !!}
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</td>
