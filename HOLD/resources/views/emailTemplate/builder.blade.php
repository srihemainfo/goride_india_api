<!DOCTYPE html>
<html>
<head>
    <title>Unlayer Email Builder</title>
    <style>
        html, body {
            margin: 0;
            height: 100%;
        }
        #editor {
            height: 100vh;
        }
    </style>
</head>
<body>

<div id="editor"></div>

<script src="https://editor.unlayer.com/embed.js"></script>
<script>
    unlayer.init({
        id: 'editor',
        projectId: 1234, // Optional: you can leave this out for free usage
        displayMode: 'email',
        features: {
            image: {
                enabled: true,
                upload: true,
                url: true,
            },
        },
        appearance: {
            theme: 'dark', // or 'light'
        },
        tools: {
            // Example of enabling/disabling tools
            image: { enabled: true },
            text: { enabled: true },
            button: { enabled: true },
            divider: { enabled: true },
        }
    });

    // Save Button
    const saveBtn = document.createElement('button');
    saveBtn.innerText = 'Save Template';
    saveBtn.style = 'position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 10px;';
    saveBtn.onclick = function () {
        unlayer.exportHtml(function(data) {
            const html = data.html;
            const design = data.design;

            // Optional: Send to Laravel backend
            fetch('/save-unlayer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    html: html,
                    design: design
                })
            }).then(res => res.json())
              .then(data => alert(data.message));
        });
    };
    document.body.appendChild(saveBtn);
</script>

</body>
</html>
