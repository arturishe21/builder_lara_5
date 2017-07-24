<div class="group" name="{{$name}}">
    <div class="other_section">

        <ul id="sortable">
            @foreach($rows as $k => $filds)
                <li>
                    <div class="section_group">
                        <a class="delete_group" onclick="TableBuilder.deleteGroup(this)"
                           style="float: right; cursor: pointer; position: absolute; right: 0; z-index: 10"><i
                                    class="fa red fa-times"></i> Удалить</a>
                        @foreach($filds as $titleField => $fild)
                            <section class="{{$fild['class_name'] or ''}}"
                                     @if(isset($fild['tabs'])) style="margin-top:20px" @endif>

                                @if (!isset($fild['tabs']))
                                    <label class="label">{{$fild['caption']}}</label>
                                @endif
                                <div style="position: relative;">
                                    <div class="tabs_section">
                                        {!! $fild['html'] !!}
                                        @if (isset($fild['multi']) && $fild['multi'])
                                            <input type="hidden" name="{{$titleField}}" value=''>
                                        @endif
                                    </div>
                                </div>
                            </section>
                        @endforeach
                    </div>
                </li>
            @endforeach

        </ul>

    </div>
    @if (!$hide_add)
        <a class="add_group" onclick="TableBuilder.addGroup(this); groupTabsRefresh('{{$name}}');"><i
                    class="fa fa-plus-square"></i> Добавить</a>
    @endif
</div>
<script>

    var i = 0;
    $(".group[name={{$name}}] input, .group[name={{$name}}] select, .group[name={{$name}}] textarea").each(function (index) {

        if ($(this).attr("name") != undefined) {
            i++;
            $(this).attr("id", "{{$name}}_" + $(this).attr("name"));
            $(this).attr("name", "{{$name}}[" + $(this).attr("name") + "][]");

            if ($(this).attr("data-multi") == 'multi') {
                $(this).removeAttr('name');
            }
            //  $(this).addClass($(this).attr("id") + '_' + i);
        }
    });

    //group for tabs
    function groupTabsRefresh(name) {

        i = 0;
        $(".group[name=" + name + "] .tabs_section").each(function () {
            i++;
            $(this).find(".nav-tabs a").each(function () {
                var hrefOld = $(this).attr("href");
                $(this).attr("href", hrefOld + "_" + i);
            });
            $(this).find(".tab-content .tab-pane").each(function () {
                var idOld = $(this).attr("id");
                $(this).attr("id", idOld + "_" + i);
            });
        });
    }
    groupTabsRefresh('{{$name}}');
</script>

<style>
    #sortable {
        list-style: none;
        margin: 0px;
        padding: 0px;
        cursor: move;
    }
</style>
<script>
    $(function () {
        $("#sortable").sortable();
        $("#sortable").disableSelection();
    });
</script>
