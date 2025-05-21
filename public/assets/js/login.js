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

    // Sample data for demo purposes
    // In a real application, this might come from an AJAX call or session storage
    const recentUsers = [
        "123456789", // Dr. John Doe
        "987654321", // Prof. Jane Smith
        "admin"      // Administrator
    ];

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

        // In a real application, you'd only store this after successful login
        // This is just a demo to show how to build history
        if (enteredNIP && !recentUsers.includes(enteredNIP)) {
            // Could store in localStorage for persistence between sessions
            if (typeof Storage !== "undefined") {
                let savedUsers = JSON.parse(localStorage.getItem("recentUsers") || "[]");
                if (!savedUsers.includes(enteredNIP)) {
                    savedUsers.unshift(enteredNIP);
                    // Keep only the most recent 5 users
                    savedUsers = savedUsers.slice(0, 5);
                    localStorage.setItem("recentUsers", JSON.stringify(savedUsers));
                }
            }
        }
    });

    // Load stored users on page load
    if (typeof Storage !== "undefined") {
        const savedUsers = JSON.parse(localStorage.getItem("recentUsers") || "[]");
        // Merge with our demo users but avoid duplicates
        savedUsers.forEach(user => {
            if (!recentUsers.includes(user)) {
                recentUsers.unshift(user);
            }
        });
    }
});