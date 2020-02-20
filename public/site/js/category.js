
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
	var cate = [];
	var availableTags = [];
	 $("#category").keypress(function() {
        var keyword = $("#category").val();
             $.get("/site/category/"+ keyword)
              .done(function(data) {
	              	$(data.category).each(function(index,category) {
	              		cate = category.category_type_name;
	              		availableTags.push(cate);

	              	});
	              console.log(availableTags);
	             	$('#category').typeahead({
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



