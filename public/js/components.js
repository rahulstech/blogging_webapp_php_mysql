var blogging = {
    TextCounter: function (inpId, outId) {
        const inp = $("#" + inpId);
        const out = $("#" + outId);
        if (inp && out)
        {
            const max = parseInt(inp.attr("maxlength")) || Infinity;
            blogging.showCounterOutput(inp,out,max);
            inp.keypress(e => {
                blogging.showCounterOutput(inp,out,max);
            })
        }
    },
    PasswordVisibilityToggle: function (btnId, inpId) {
        const btn = document.querySelector("#" + btnId);
        const inp = document.querySelector("#" + inpId);
        if (btn && inp) {
            btn.addEventListener("change", e => {
                if (btn.checked) {
                    inp.type = "text";
                }
                else {
                    inp.type = "password";
                }
            })
        }
    },
    SidebarToggleButton: function (btnId) {
        const sidebarToggle = document.body.querySelector("#" + btnId);
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', event => {
                event.preventDefault();
                document.body.classList.toggle('sb-sidenav-toggled');
            });
        }
    },
    showCounterOutput: function (inp,out,max) {
        var counter = max === Infinity ? inp.val().length : inp.val().length+"/"+max;
        out.html(counter);
    }
};