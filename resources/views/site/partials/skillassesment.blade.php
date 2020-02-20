<section class="applySection">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="col-md-12 col-sm-12 col-xs-12 sort">
                <h3 class="">Skill Summary</h3>
                <div class="alert alert-warning">
                    To complete this section, add at least 10 skills you have using the form below.
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12" id="skills-outer-container">
                @include('site/partials/skill-assessment')
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <h4 class="add-skill-header">Add New Skill</h4>
                <form class="add-skill" method="post" action="{{URL::route('site-add-skill-assessment')}}">
                    <div>
                        <select required name="category_id" id="category_id">
                            <option value="">Select Category</option>
                            @foreach($skill_groups as $key => $value)
                                <option value="{{$value->id}}">{{$value->skill_group_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="item">
                        <select required name="skill_id" id="skill_id">
                            <option value="">Select Skill*</option>
                        </select>
                    </div>
                    <div class="item">
                        <select required name="recency_id">
                            <option value="">How recent*</option>
                            @foreach($recency as $id => $name)
                                <option value="{{$id}}">{{$name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="item">
                        <a class="missing-skill" href="#">Can't find a skill?</a>
                    </div>
                    <div class="item">
                        &nbsp;
                    </div>
                    <div class="item">
                        <select required name="frequency_id">
                            <option value="">How frequent*</option>
                            @foreach($frequency as $id => $name)
                                <option value="{{$id}}">{{$name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="item">
                        <select required name="level_id">
                            <option value="">Current level of performance*</option>
                            @foreach($level as $id => $name)
                                <option value="{{$id}}">{{$name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <textarea name="comment" placeholder="Add Comment" maxlength="2000"></textarea>
                    </div>
                    <div class="clear-link">
                        <a href="#" class="clear-skill">Clear</a>
                    </div>
                    <div class="submit-skill">
                        <img id='processing_loader' class='processing'
                             src="{{Config::get('app.url')}}/site/img/loading.gif" style="display:none;">
                        <button type="submit">Add New Skill</button>
                    </div>
                    <input type="hidden" id="token" name="_token" value="{{{csrf_token() }}}">
                </form>
            </div>
        </div>
    </div>
</section>

<?php
// Lets load all the ID's present in the array!
$skill_groups_id = [];
collect($skill_groups)->map(function ($item) use (&$skill_groups_id) {
    $skill_groups_id[] = $item->id;
});
?>
<script type="text/javascript" src="{{Config::get('app.url')}}/site/js/bootstrap-notify.min.js"></script>
<script type="text/javascript">
    var skills = [];
    @foreach ($skill_groups_id as $groupId)
        skills[{{$groupId}}] = [];
    @endforeach


    <?php foreach($skill_list as $skill): ?>
        <?php if(in_array($skill->job_category_id, $skill_groups_id)): ?>
        skills[{{$skill->job_category_id}}].push({id: {{$skill->id}}, name: '{{$skill->skil_names}}'});
    <?php endif ?>
    <?php endforeach ?>

    /**
     * This function is called from profile.js when the skill is added using the Select element.
     *
     * @param skillId
     * @param skillCategoryId
     */
    window.removeSkill = function(skillId, skillCategoryId) {
        // Lets walk through the array to find the right sub skill to be removed
        skills[skillCategoryId].forEach(function (skill, index) {
            // If we have found the skill to be removed lets remove it
            if(skillId == skill.id) {
                skills[skillCategoryId].splice(index, 1)
            }
        });
    }
</script>
<div id="skill-suggestion-outer">
    <div class="skill-suggestion">
        <div class="close-suggest">X</div>
        <h4>Request a new skill to be added</h4>
        <p>If you believe there should be a skill that isn't listed, please submit this form to request the addition of
            this skill.</p>
        <form class="suggest-skill" method="post" action="{{URL::route('site-suggest-skill')}}">
            <div>
                <input type="text" name="skill_name" placeholder="Enter Skill Name" required>
            </div>
            <div>
                <select name="category_id" required>
                    <option value="">Select Category</option>
                    @foreach($skill_groups as $key => $value)
                        <option value="{{$value->id}}">{{$value->skill_group_name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <textarea name="description" placeholder="Description" maxlength="2000"></textarea>
            </div>
            <div class="submit-suggest">
                <button type="submit" name="submit">Submit</button>
            </div>
            <input type="hidden" id="token" name="_token" value="{{{csrf_token() }}}">
        </form>
    </div>
</div>