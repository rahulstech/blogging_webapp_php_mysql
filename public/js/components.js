var blogging = {
    TextCounter: function (inpId, outId) {
        const inp = document.getElementById(inpId);
        const out = document.getElementById(outId);
        if (inp && out)
        {
            const max = parseInt(inp.getAttribute("maxlength")) || Infinity;
            const callback = counter => {
                var state = max === Infinity ? counter.characters : counter.all+"/"+max;
                out.innerHTML = state;
            };

            Countable.count(inp, callback);
            Countable.on(inp,callback);
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
    }
};