@foreach ($skills as $key => $skill)
    <div class="skill-group-container">
        <div class="skill-group-header">
            {{$key}}
        </div>
        <div class="skill-group-count">
        @if (count($skill) > 1)
            {{count($skill)}} Skills
        @else
            {{count($skill)}} Skill
        @endif
        </div>
        <div class="skill-group-toggle glyphicon glyphicon-menu-down"></div>
        <div style="clear: both;"></div>
        <div class="skill-group-inner">
            @foreach($skill as $s)
            <form class="update-skill" method="post" action="{{URL::route('site-update-skill-assessment', ['id' => $s->id])}}">
                <input type="hidden" name="skill_group_category_id"
                       value="<?=!empty($s->skill_group_id) ? $s->skill_group_id : ""?>"/>
                <input type="hidden" name="skill_assessment_type_id"
                       value="<?=!empty($s->skill_asse_type_id) ? $s->skill_asse_type_id : ""?>"/>
                <input type="hidden" name="skill_name"
                       value="<?=!empty($s->skil_names) ? $s->skil_names : ""?>"/>
                <div class="skill">
                    <div class="name">
                        {{$s->skil_names}}
                    </div>
                    <div class="actions">
                        <a href="#" class="view-skill">View</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" class="remove-skill">Remove</a>
                    </div>
                    <div style="clear: both;"></div>
                </div>
                <div class="skill-form">
                    <div class="item">
                        <select required name="recency_id">
                            @foreach($recency as $id => $name)
                            @if ($id == $s->recency_id)
                            <option value="{{$id}}" selected>{{$name}}</option>
                            @else
                            <option value="{{$id}}">{{$name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="item">
                        <select required name="frequency_id">
                            @foreach($frequency as $id => $name)
                            @if ($id == $s->frequency_id)
                            <option value="{{$id}}" selected>{{$name}}</option>
                            @else
                            <option value="{{$id}}">{{$name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="item">
                        <select required name="level_id">
                            @foreach($level as $id => $name)
                            @if ($id == $s->level_id)
                            <option value="{{$id}}" selected>{{$name}}</option>
                            @else
                            <option value="{{$id}}">{{$name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <textarea name="comment" maxlength="2000">{{$s->comment}}</textarea>
                    </div>
                    <div class="update-skill-actions">
                        <button type="button" class="cancel-update-skill">Cancel</button>
                        <button type="submit" class="save-update-skill">Save</button>
                    </div>
                    <div style="clear: both;"></div>
                    <input type="hidden" name="_token" value="{{{csrf_token() }}}">
                </div>
            </form>
            @endforeach
        </div>
    </div>
@endforeach