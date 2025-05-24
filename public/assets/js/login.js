document.addEventListener('DOMContentLoaded', function () {
    // Make sure jQuery and jQuery UI are loaded
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded. Autocomplete will not work.');
        return;
    }

    if (typeof jQuery.ui === 'undefined') {
        console.error('jQuery UI is not loaded. Autocomplete will not work.');
        return;
    }

    // Load stored users from localStorage
    let recentUsers = [];
    if (typeof Storage !== "undefined") {
        recentUsers = JSON.parse(localStorage.getItem("recentUsers") || "[]");
    }

    // Add default admin user if in development
    if (!recentUsers.includes("199001012015041001")) {
        recentUsers.push("199001012015041001");
    }

    // Initialize autocomplete
    $("#nip").autocomplete({
        source: function (request, response) {
            // Filter users that match the current input
            const term = request.term.toLowerCase();
            const matches = recentUsers.filter(user =>
                user.toLowerCase().indexOf(term) !== -1
            );

            response(matches);
        },
        minLength: 1, // Start showing suggestions after typing 1 character
        delay: 100,   // Milliseconds to wait before searching
        select: function (event, ui) {
            // When user selects an option
            $("#nip").val(ui.item.value);
            return false;
        },
        position: {
            my: "left top",
            at: "left bottom"
        }
    });

    // Store successful logins for future autocomplete
    $("form").on("submit", function () {
        const enteredNIP = $("#nip").val();

        // Store NIP in localStorage for future autocomplete
        if (enteredNIP && typeof Storage !== "undefined") {
            let savedUsers = JSON.parse(localStorage.getItem("recentUsers") || "[]");
            // Remove this NIP if it already exists (so we can put it at the front)
            savedUsers = savedUsers.filter(user => user !== enteredNIP);
            // Add to beginning of array
            savedUsers.unshift(enteredNIP);
            // Keep only the most recent 5 users
            savedUsers = savedUsers.slice(0, 5);
            localStorage.setItem("recentUsers", JSON.stringify(savedUsers));
        }
    });
});