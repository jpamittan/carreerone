$(document).ready(function() {
  $( "#editprof").click(function( event ) {
    event.preventDefault();
     $( ".profileview" ).hide();
    $( ".profilesubmit" ).show();

  });

  var formEdit = false;

    $(document).on('change', '#category_id', function (event) {
        var id = $(this).val(),
            select = $('#skill_id');

        select.find('option[value!=""]').remove();

        if (!id) {
            return;
        }

        $.each(skills[id], function (k, v) {
            select.append($('<option>', {value: v.id, text: decodeHtml(v.name)}));
        });
    });

  function decodeHtml(html) {
      var txt = document.createElement("textarea");
      txt.innerHTML = html;
      return txt.value;
  }

  $(window).on('beforeunload', function(event) {
      if (formEdit) {
          return true;
      }
  });

  $(document).on('change', '.add-skill input, .add-skill select, .add-skill textarea', function(event) {
      formEdit = true;
  });

    // Adding a listener on $('body') as the elements being monitored here are not always visible on the page
    // this will listen to them when they are added on to the page. If a change is made in one of these fields
    // the redirect error whill popup
    // Solution for INS-148
    $('body').on('change', 'input[type=text][name=mobilenumber], ' +
        'input[type=text][name=phonumber],input[type=text][name=personalemail],' +
        'input[type=text][name=new_emergencycontactname],' +
        'input[type=text][name=new_emergencycontactnumber],' +
        'input[type=text][name=new_emergencyrelationship],' +
        'input[type=text][name=new_emergencyemail]', function (event) {
        formEdit = true;
    });

  $(document).on('click', '.clear-skill', function(event) {
      event.preventDefault();

      $('.add-skill').trigger('reset');

      formEdit = false;
  });

  $(document).on('click', '.submit-skill', function(event) {
      var form = $(this).parents('form');
      $('#processing_loader').show();

      if (form[0].checkValidity()) {
          event.preventDefault();

          $.post(form.attr('action'), form.serialize(), function(data) {
              // @ToDo: Lets remove the Sub Skill choosen
              var selectedSkillId = $($('#skill_id')[0].options[$('#skill_id')[0].selectedIndex]).attr('value');
              $($('#skill_id')[0].options[$('#skill_id')[0].selectedIndex]).remove();

              // Lets remove that skill form the list of available skills
              window.removeSkill(selectedSkillId, $('select#category_id').val());

              $('#skills-outer-container').html(data);
              $('.clear-skill').trigger('click');
              formEdit = false;
              $('#processing_loader').hide();
              $.notify({
                  message: 'Skill successfully added'
              }, {
                  type: 'success'
              });

              // If we are adding our first skills, lets reload the page so we remove the error form the top of the page.
              if($('.update-skill') && $('.update-skill').length == 10) {
                  window.location.reload();
              }
          });
      }else{
         $('#processing_loader').hide();
      }

  });

  $(document).on('click', '.skill-group-header, .skill-group-toggle', function(event) {
      $(this).siblings('.skill-group-inner').toggle();
  });

  $(document).on('click', '.view-skill', function(event) {
      $(this).parent().hide();
      $(this).parents('.skill').next().show();

      event.preventDefault();
  });

  $(document).on('click', '.cancel-update-skill', function(event) {
      $(this).parents('.skill-form').hide();
      $(this).parents('.skill-form').prev().find('.actions').show();

      event.preventDefault();
  });

  $(document).on('click', '.save-update-skill', function(event) {
      var $this = $(this);

      $.post($this.parents('.update-skill').attr('action'), $this.parents('.update-skill').serialize() + '&_method=put', function(data) {
          $this.parents('.skill-form').hide();
          $this.parents('.skill-form').prev().find('.actions').show();

          $.notify({
              message: 'Skill successfully updated'
          }, {
              type: 'success'
          });
      });

      event.preventDefault();
  });

  $(document).on('click', '.remove-skill', function(event) {
      var $form = $(this).parents('.update-skill');

      if ($('.update-skill').length == 10) {
          alert('You must have a minimum of 10 skills defined. Please add another skill in order to remove this skill');

          return false;
      }

      if (window.confirm('Are you sure you wish to remove this skill?')) {
          $.post($form.attr('action').replace('update', 'delete'), {_token: $('#token').val(), _method: 'delete'}, function(data) {
              var count = $form.parent().children().length;

              if (count == 1) {
                  $form.parents('.skill-group-container').remove();
              } else if (count == 2) {
                  $form.parents('.skill-group-container').find('.skill-group-count').text('1 Skill')
              } else {
                  $form.parents('.skill-group-container').find('.skill-group-count').text(--count + ' Skills')
              }

              // Lets load the parent category here form the form
              var $parentCategoryId = $form.find('input[type=hidden][name=skill_group_category_id]')[0].value,
                  $skillAssessmentTypeId = $form.find('input[type=hidden][name=skill_assessment_type_id]')[0].value,
                  $skill_name = $form.find('input[type=hidden][name=skill_name]')[0].value;

              // This skills variable is store in the profile.blade.php
              // @ToDo: Make that app variable in JS
              if(!skills[$parentCategoryId]) {
                  skills[$parentCategoryId] = [];
              }

              // Lets add the skill back to the Skill object in profile.blade.php
              skills[$parentCategoryId].push({id: $skillAssessmentTypeId, name: $skill_name});

              $form.remove();

              $.notify({
                  message: 'Skill successfully removed'
              }, {
                  type: 'success'
              });



          });
      }

      event.preventDefault();
  });

  $(document).on('click', '.missing-skill', function(event) {
      $('#skill-suggestion-outer').fadeIn();

      event.preventDefault();
  });

  $(document).on('click', '.close-suggest', function(event) {
      $('#skill-suggestion-outer').fadeOut();
  });

  $(document).on('click', '#skill-suggestion-outer', function(event) {
      if ($(event.target).attr('id') == 'skill-suggestion-outer') {
          $('#skill-suggestion-outer').fadeOut();
      }
  });

  $(document).on('click', '.submit-suggest', function(event) {
      var $form = $(this).parents('.suggest-skill');

      if ($form[0].checkValidity()) {

          $.post($form.attr('action'), $form.serialize(), function(data) {
              $('#skill-suggestion-outer').fadeOut();

              $form.trigger('reset');

              $.notify({
                  message: 'Your request for new skill is successfully submitted.',
              }, {
                  type: 'success'
              })
          });

          event.preventDefault();
      }
  });

  $(document).on('change', '#ins_disability', function(event) {
      if ($(this).val() == 1) {
          $('.reasonable-adjustment').show();
      } else {
          $('.reasonable-adjustment').hide();
          $('.reasonable-adjustment-info').hide();
          $('#ins_disability_adjustment').val('');
      }
  });

  $(document).on('change', '#ins_disability_adjustment', function(event) {
      if ($(this).val() == 1) {
          $('.reasonable-adjustment-info').show();
      } else {
          $('.reasonable-adjustment-info').hide();
      }
  });

  $('[data-toggle="tooltip"]').popover({trigger: 'hover click'});

  $( "#profilesubmitcancel").click(function( event ) {

    event.preventDefault();
     $( ".profileview" ).show();
    $( ".profilesubmit" ).hide();

  });

  $('.toggle-mismatch').bind('click', function(event) {
    var $this = $(this);

    if ($this.hasClass('active')) {
      $this.removeClass('active');
      $('#mismatch-' + $this.data('id')).hide();
    } else {
      $this.addClass('active');
      $('#mismatch-' + $this.data('id')).show();
    }

    event.preventDefault();
  });


$(document).on('click', '.deletecategory', function(event) {
	var id = $(this).data('category-id');

   event.preventDefault();

   size =  $( ".deletecategory" ).size() ;
   if(size <= 1){
    alert('At least one category is required. Please add another before removing this one');
      return false;
    }

    if (! confirm('Are you sure you wish to remove this category?')) {
        return false;
    }

      var token = $("#token");

    var data = "id=" + id  + "&_token=" + token.val();
 	$.ajax({
                type: "POST",
                url: "/site/delete_user_category",
                data:data,
                success: function (data) {
                      $('.category-section').replaceWith(data);


                      $.notify({
                          message: 'Category removed successfully'
                      }, {
                          type: 'success'
                      });
                }
            });
});



$(document).on('click', '.add-category', function(e) {
   e.preventDefault();

    if($('#sel1category').val() == 0){
        alert('Please select category');
        return false;
      }

   if($('#popup-category') .val() == 1) {
        $('#myModalCategory').modal('show');
          $('#category_id').val($('#sel1category').val());
          $('#popup-category-val').val(1);

      return false;
   }else{

       $('#processing_loader_occupational_category').show();
      var $form = $('#add-category-frm');

      $.post($form.attr('action'), $form.serialize(), function(data) {
        if (data.indexOf('section') !== -1) {
            $('.category-section').replaceWith(data);

            $.notify({
                message: 'Category successfully added'
            }, {
                type: 'success'
            });

            $('#processing_loader_occupational_category').hide();
        } else {
            $.notify({
                message: data
            }, {
                type: 'warning'
            });
            $('#processing_loader_occupational_category').hide();
        }
      });
   }

});

$(document).on('click', '#add_category_btn', function(e) {
      e.preventDefault();


      $('#category_id').val($('#sel1category').val());

    imnotinterested_txt =  ($('#add_category_txt').val());

    if(imnotinterested_txt == ''){
      $('#add_category_txt').addClass('alert alert-danger');
      return false;
    }else{
      $('#add_category_btn').html('Processing...');
      $('#add_category_btn').prop("disabled",true);
      $('#add_category_cancel').hide();

      $('#imnotinterested_txt').removeClass('alert alert-danger');

      var $form = $('#add-category-form');

      $.post($form.attr('action'), $form.serialize(), function(data) {
        if (data.indexOf('section') !== -1) {
            $('#myModalCategory').modal('hide');

            $('.category-section').replaceWith(data);

            $.notify({
                message: 'Category successfully added'
            }, {
                type: 'success'
            });
        } else {
            $.notify({
                message: data
            }, {
                type: 'warning'
            });
        }
      });

     // $('#Mcategory').val($('#sel1category').val());
      /*var formdata = $( "#rejected-form" ).serialize();
      $.ajax({
                method: "POST",
                  data:formdata,
                url: "/site/",
                success: function (msg) {
                    if (msg.success == true) {
                       $('#myModal').hide();
                      //  window.location.href = '/site/getJobs/'+{{$job->id}};
                        window.location.href = '/site/profile';

                    } else {
                      $('#add_category_btn').html('Submit');
            $('#add_category_btn').prop("disabled",false);
            $('#add_category_cancel').show();
                    }
                },
                error: function () {
                  $('#add_category_btn').html('Submit');
          $('#add_category_btn').prop("disabled",false);
          $('#add_category_cancel').show();

                },
            });
*/

    }



  });




$(document).on('click', '.deletelocation', function(e) {
    e.preventDefault();
    var id = $(this).data('location-id');
    size =  $( ".deletelocation" ).size() ;

    if(size <= 1){
        alert('At least one location is required. Please add another before removing this one.');
        $('#processing_loader_resume_remove_'+id).hide();
        return false;
    }

    var confirmed = confirm('Are you sure to wish to remove this location?');

  if(confirmed){

      $('#processing_loader_resume_remove_'+id).show();

        var token = $("#token");
        var data = "id=" + id  + "&_token=" + token.val();
        $.ajax({
                    type: "POST",
                    url: "/site/delete_user_location",
                    data:data,
                    success: function (data) {

                      $('.location-section').replaceWith(data);


                      $.notify({
                          message: 'Location removed successfully'
                      }, {
                          type: 'success'
                      });


                   //  location.reload();

                    },
                    error: function () {
                        $('#processing_loader_resume_remove_'+id).hide();
                    },
                });

      }else{
        return false;
      }
})


$(document).on('click', '.add_location', function(event) {
    event.preventDefault();

    $('#processing_loader_resume_add' ).show();

    if ($('#location').val() == 0) {
        $.notify({
            message: 'Please select a location'
        }, {
            type: 'warning'
        });

        $('#processing_loader_resume_add').hide();

        return false;
    }

    var $form = $(this).parents('form');

    $.post($form.attr('action'), $form.serialize(), function(data) {
        if (data.indexOf('section') !== -1) {
            $('.location-section').replaceWith(data);

            $.notify({
                message: 'Location successfully added'
            }, {
                type: 'success'
            });

            // Lets check if we have only one location lets reload the page so we can get rid of the error
            var locationsLoaded  = $('#location_container > .added_location');
            if(locationsLoaded && locationsLoaded.length == 1) {
                window.location.reload();
            }

        } else {
            $.notify({
                message: data
            }, {
                type: 'warning'
            });

            $('#processing_loader_resume_add').hide();
        }
    });
})

$(document).on('click', '.deleteresume', function(event) {
    var id = $(this).data('resume-id');
    var token = $("#token");
    var data = "id=" + id  + "&_token=" + token.val();

    event.preventDefault();

    if (confirm('Are you sure, you want to remove this resume?')) {
        $.notify({
            message: "Removing resume please wait..."
        }, {
            type: 'warning'
        });

        $.ajax({
            type: "POST",
            url: "/site/delete_user_resume",
            data: data,
            success: function (data) {
                $('.resume-upload').replaceWith(data);
                $.notify({
                    message: 'Resume removed successfully!'
                }, {
                    type: 'success'
                });
            }
        });
    }
});

$(document).on('click', '.upload_resume_btn', function(event) {
    event.preventDefault();

    var $form = $(this).parents('form');

    $('#processing_loader_resume_upload').show();

    $.ajax({
        url: $form.attr('action'),
        type: 'POST',
        data: new FormData($form[0]),
        cache: false,
        dataType: 'json',
        processData: false,
        contentType: false,
        xhrFields: {
            withCredentials: true
        },
        complete: function(data) {
            if (data.responseText.indexOf('section') !== -1) {
                $('.resume-upload').replaceWith(data.responseText);

                $.notify({
                    message: 'Resume upload successful'
                }, {
                    type: 'success'
                });
            } else {
                $.notify({
                    message: data.responseText
                }, {
                    type: 'warning'
                });

                $('#processing_loader_resume_upload').hide();
            }
        }
    });
});


    $('#submit-profile-edit').click(function (event) {
        formEdit = false;
        event.preventDefault();
        var oldemail = $('#old_personalemail').val(),
            newemail = $('#personalemail').val(), $this = $(this),
            $validationErrorContainer = $('#validationErrorContainer');

        // Lets empty the error container so we can add any news validation errors.
        $validationErrorContainer.empty().hide();

        var confirmEmailChange = true;
        if ($.trim(oldemail) != $.trim(newemail)) {
            confirmEmailChange = confirm('Are you sure to change email address');
        }

        if (confirmEmailChange) {
            var $form = $this.parents('form');

            $this.val('Saving...');

            $.post($form.attr('action'), $form.serialize(), function (data) {
                $.notify({
                    message: 'Profile successfully updated'
                }, {
                    type: 'success'
                });

                $this.val('Submit');

                $('#profile-view').html(data);

                $('#profilesubmitcancel').trigger('click');

                // All we do is reload the page so we can get the right Error(s).
                window.location.reload();

            }).fail(function (response) {
                // Lets check if we have run into a Validation error thrown by Laravel
                if (response && response.status && response.status == 422) {
                    $validationErrorContainer.empty().hide();

                    // Lets load the responseJSON
                    Object.keys(response.responseJSON).forEach(function (element, key) {
                        // Lets load the error messages.
                        if (response.responseJSON[element]) {
                            response.responseJSON[element].forEach(function (element, key) {
                                $validationErrorContainer.append($('<div/>').attr('class', 'alert alert-danger').attr('role', 'alert').text(element));
                            });
                        }
                    });

                    $validationErrorContainer.show();
                }
                $this.val('Save');

                // Lets scroll the body to the top
                $("html, body").animate({ scrollTop: 0 }, "slow");
            });
        } else {
            return false;
        }
    });

});
