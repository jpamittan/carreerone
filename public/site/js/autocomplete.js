$( document ).ready(function() {
var substringMatcher = function(strs) {
  return function findMatches(q, cb) {
    var matches, substringRegex;

    // an array that will be populated with substring matches
    matches = [];

    // regex used to determine if a string contains the substring `q`
    substrRegex = new RegExp(q, 'i');

    // iterate through the pool of strings and for any string that
    // contains the substring `q`, add it to the `matches` array
    $.each(strs, function(i, str) {
      if (substrRegex.test(str)) {
        matches.push(str);
      }
    });

    cb(matches);
  };
};
	var tags = [];
	var availableTags = [];
	 $("#location").keypress(function() {
        var keyword = $("#location").val();
            $.get("/site/location_keywords/"+ keyword)
              .done(function(data) {
	              	$(data.location).each(function(index,location) {
	              		tags = location.combi;
	              		availableTags.push(tags);
	              	});
	              console.log(availableTags);
	             	$('#location').typeahead({
						hint: true,
						highlight: true,
						minLength: 1
					},
					{
					name: 'availableTags',
					source: substringMatcher(availableTags)
					});  
	              		
	              	 
	              	 
              });

       
    });
	 



});