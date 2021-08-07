 $(function(){
      // using default options
      $("#tree").fancytree({
    activeVisible: true, // Make sure, active nodes are visible (expanded).
    aria: false, // Enable WAI-ARIA support.
    autoActivate: true, // Automatically activate a node when it is focused (using keys).
    autoCollapse: false, // Automatically collapse all siblings, when a node is expanded.
    autoScroll: false, // Automatically scroll nodes into visible area.
    clickFolderMode: 4, // 1:activate, 2:expand, 3:activate and expand, 4:activate (dblclick expands)
    checkbox: false, // Show checkboxes.
    debugLevel: 2, // 0:quiet, 1:normal, 2:debug
    disabled: false, // Disable control
    focusOnSelect: false, // Set focus when node is checked by a mouse click
    generateIds: true, // Generate id attributes like <span id='fancytree-id-KEY'>
      idPrefix: "id", // Used to generate node id´s like <span id='fancytree-id-<key>'>.
    icon: false, // Display node icons.
    keyboard: true, // Support keyboard navigation.
    keyPathSeparator: "/", // Used by node.getKeyPath() and tree.loadKeyPath().
    minExpandLevel: 1, // 1: root node is not collapsible
    quicksearch: false, // Navigate to next node by typing the first letters.
    selectMode: 2, // 1:single, 2:multi, 3:multi-hier
    tabindex: 0, // Whole tree behaves as one single control
    titlesTabbable: false, // Node titles can receive keyboard focus
  //
  
  extensions: ["filter"],
			quicksearch: true,
			//source: {
			//	url: "ajax-tree-local.json"
			//},
			filter: {
				autoApply: true,   // Re-apply last filter if lazy data is loaded
				autoExpand: true, // Expand all branches that contain matches while filtered
				counter: true,     // Show a badge with number of matching child nodes near parent icons
				fuzzy: false,      // Match single characters in order, e.g. 'fb' will match 'FooBar'
				hideExpandedCounter: true,  // Hide counter badge if parent is expanded
				hideExpanders: false,       // Hide expanders if all child nodes are hidden by filter
				highlight: true,   // Highlight matches by wrapping inside <mark> tags
				leavesOnly: false, // Match end nodes only
				nodata: true,      // Display a 'no data' status node if result is empty
				mode: "dimm"       // Grayout unmatched nodes (pass "hide" to remove unmatched node instead)
			},
  
  
  //
          
          
    }
      
      );
  //####    
  $("input[name=search]").on("keyup", function(e){
			var n,
				tree = $.ui.fancytree.getTree(),
				args = "autoApply autoExpand fuzzy hideExpanders highlight leavesOnly nodata".split(" "),
				opts = {},
			filterFunc = tree.filterBranches, //or filterNodes,
				match = $(this).val();

			
			//opts.mode = "hide";// or "dimm";
            opts.mode = "dimm";
			//if(e && e.which === $.ui.keyCode.ESCAPE || $.trim(match) === ""){
			//	$("button#btnResetSearch").click();
			//	return;
			//}
			
				// Pass a string to perform case insensitive matching
				n = filterFunc.call(tree, match, opts);
			
			$("button#btnResetSearch").attr("disabled", false);
			$("span#matches").text("(" + n + " matches)");
		}).focus();
			
      
  //####    
    });
