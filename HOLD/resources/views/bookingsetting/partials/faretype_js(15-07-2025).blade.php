<script>
    document.addEventListener('DOMContentLoaded', function () {
        const switches = document.querySelectorAll('.fare-switch');
        const updateButton = document.getElementById('updateButton');

        // Toggle switch logic
        switches.forEach((toggle) => {
            toggle.addEventListener('change', function () {
                if (this.checked) {
                    switches.forEach((el) => {
                        if (el !== this) el.checked = false;
                    });
                    updateButton.style.display = 'inline-block';
                } else {
                    const isAnyChecked = Array.from(switches).some((el) => el.checked);
                    updateButton.style.display = isAnyChecked ? 'inline-block' : 'none';
                }
            });
        });

        // Load current fare_type
        function showlist() {
            var formDataObject = {
                token: getCookie('d_token'),
                device_id: 0
            };

            $.ajax({
                url: '{{ env("API_URL") }}faretype',
                method: 'POST',
                data: formDataObject,
                success: function (response) {
                    if (response.status === 200 && response.data) {
                        const fareType = response.data.fare_type;

                        // Reset all switches
                        document.getElementById('kmBased').checked = false;
                        document.getElementById('hourlyBased').checked = false;
                        document.getElementById('tariffBased').checked = false;

                        if (fareType == '1') {
                            document.getElementById('kmBased').checked = true;
                        } else if (fareType == '2') {
                            document.getElementById('hourlyBased').checked = true;
                        } else if (fareType == '3') {
                            document.getElementById('tariffBased').checked = true;
                        }

                        const isAnyChecked = Array.from(switches).some((el) => el.checked);
                        updateButton.style.display = isAnyChecked ? 'inline-block' : 'none';
                    }
                },
                error: function (error) {
                    console.error("API Error:", error);
                }
            });
        }

        showlist();

        // 🔁 Send updated fare_type on Update button click
        updateButton.addEventListener('click', function (e) {
            e.preventDefault();
        
            let selectedFareType = null;
            if (document.getElementById('kmBased').checked) {
                selectedFareType = '1';
            } else if (document.getElementById('hourlyBased').checked) {
                selectedFareType = '2';
            } else if (document.getElementById('tariffBased').checked) {
                selectedFareType = '3';
            }
        
            if (!selectedFareType) {
                alert("Please select a fare type.");
                return;
            }
        
            $.ajax({
                url: '{{ env("API_URL") }}faretype_update',
                method: 'POST',
                data: {
                    fare_type: selectedFareType,
                    token: getCookie('d_token'),
                    device_id: 0
                },
                success: function (response) {
                    if (response.status === 200) {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Success',
                            text: 'Fare type updated successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        });
        
                        // Refresh the UI
                        showlist();
                    } else {
                        alert("Update failed. Please try again.");
                    }
                },
                error: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'Error',
                        text: "Something went wrong. Please check your connection.",
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        });

    });
</script>
