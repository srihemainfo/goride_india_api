@extends('dashboard-layout.index')

@section('content')

<style>
    .car-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 20px;
        margin: 20px;
    }

    .car-card {
        flex: 0 0 32%;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
        border: 1px solid #ddd;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        position: relative;
    }

    .image-box {
        width: 100%;
        height: 180px;
        background: #f9f9f9;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
    }

    .image-box img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }

    table {
        width: 100%;
        font-size: 14px;
        padding: 15px 20px;
        border-collapse: collapse;
    }

    table td {
        padding: 8px 15px;
        vertical-align: top;
    }

    .car-label {
        font-weight: 500;
        color: #333;
        width: 40%;
    }

    .car-colon {
        width: 5%;
    }

    .car-value {
        width: 55%;
        color: #555;
    }

    .card-footer {
        text-align: center;
        padding: 10px;
        border-top: 1px solid #eee;
        background: #fdfdfd;
    }

    .card-footer button {
        background: #007bff;
        border: none;
        border-radius: 5px;
        padding: 6px 14px;
        color: white;
        font-weight: bold;
        cursor: pointer;
        margin: 0 5px;
    }

    .card-footer .bg-warning {
        background-color: #ffc107 !important;
    }

    .card-footer .bg-danger {
        background-color: #dc3545 !important;
    }

    .toggle-switch {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 42px;
        height: 22px;
    }

    .switch input {
        display: none;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 20px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    .switch input:checked + .slider {
        background-color: #4CAF50;
    }

    .switch input:checked + .slider:before {
        transform: translateX(20px);
    }
</style>

<div class="car-container">

    <!-- Car 1 -->
    <div class="car-card">
        <div class="toggle-switch">
            <label class="switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
            </label>
        </div>
        <div class="image-box">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTBJX8SFu5NpQAX20RQwW0hf7LAs8rc8Yt1NLJcQLE5oUpRfLILmTuhpCG68LYXdwLTTDQ&usqp=CAU" alt="Innova">
        </div>
        <table>
            <tr>
                <td class="car-label">Car Name</td>
                <td class="car-colon">:</td>
                <td class="car-value">Toyota Innova</td>
            </tr>
            <tr>
                <td class="car-label">Model</td>
                <td class="car-colon">:</td>
                <td class="car-value">2023</td>
            </tr>
            <tr>
                <td class="car-label">Car No</td>
                <td class="car-colon">:</td>
                <td class="car-value">TN-01-AB-1234</td>
            </tr>
        </table>
        <div class="card-footer">
            <button class="bg-warning">Edit</button>
            <button class="bg-danger">Delete</button>
        </div>
    </div>

    <!-- Car 2 -->
    <div class="car-card">
        <div class="toggle-switch">
            <label class="switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
            </label>
        </div>
        <div class="image-box">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTxDKh6OaVzeV9ZginE5ZrUN2OHBs8rMl6QBtwXMra76O39A2v-4v0Jo328svA2kqMycBc&usqp=CAU" alt="Swift">
        </div>
        <table>
            <tr>
                <td class="car-label">Car Name</td>
                <td class="car-colon">:</td>
                <td class="car-value">Maruti Swift</td>
            </tr>
            <tr>
                <td class="car-label">Model</td>
                <td class="car-colon">:</td>
                <td class="car-value">2022</td>
            </tr>
            <tr>
                <td class="car-label">Car No</td>
                <td class="car-colon">:</td>
                <td class="car-value">TN-02-CD-4567</td>
            </tr>
        </table>
        <div class="card-footer">
            <button class="bg-warning">Edit</button>
            <button class="bg-danger">Delete</button>
        </div>
    </div>

    <!-- Car 3 -->
    <div class="car-card">
        <div class="toggle-switch">
            <label class="switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
            </label>
        </div>
        <div class="image-box">
            <img src="https://bsmedia.business-standard.com/_media/bs/img/article/2023-12/20/thumb/facecrop/1200X900/1703059501-1499.jpg" alt="Honda City">
        </div>
        <table>
            <tr>
                <td class="car-label">Car Name</td>
                <td class="car-colon">:</td>
                <td class="car-value">Honda City</td>
            </tr>
            <tr>
                <td class="car-label">Model</td>
                <td class="car-colon">:</td>
                <td class="car-value">2023</td>
            </tr>
            <tr>
                <td class="car-label">Car No</td>
                <td class="car-colon">:</td>
                <td class="car-value">TN-03-EF-7890</td>
            </tr>
        </table>
        <div class="card-footer">
            <button class="bg-warning">Edit</button>
            <button class="bg-danger">Delete</button>
        </div>
    </div>

    <!-- Car 4 -->
    <div class="car-card">
        <div class="toggle-switch">
            <label class="switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
            </label>
        </div>
        <div class="image-box">
            <img src="https://www.carbike360.com/_next/image?url=https%3A%2F%2Fdelen.s3.ap-southeast-1.amazonaws.com%2Fsmall_PUNCH_074ded8c69.jpg&w=3840&q=75" alt="Tata Punch">
        </div>
        <table>
            <tr>
                <td class="car-label">Car Name</td>
                <td class="car-colon">:</td>
                <td class="car-value">Tata Punch</td>
            </tr>
            <tr>
                <td class="car-label">Model</td>
                <td class="car-colon">:</td>
                <td class="car-value">2024</td>
            </tr>
            <tr>
                <td class="car-label">Car No</td>
                <td class="car-colon">:</td>
                <td class="car-value">TN-04-GH-1122</td>
            </tr>
        </table>
        <div class="card-footer">
            <button class="bg-warning">Edit</button>
            <button class="bg-danger">Delete</button>
        </div>
    </div>

    <!-- Car 5 -->
    <div class="car-card">
        <div class="toggle-switch">
            <label class="switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
            </label>
        </div>
        <div class="image-box">
            <img src="https://www.godigit.com/content/dam/godigit/directportal/en/tata-safari-adventure-brand.jpg" alt="Tata Safari">
        </div>
        <table>
            <tr>
                <td class="car-label">Car Name</td>
                <td class="car-colon">:</td>
                <td class="car-value">Tata Safari</td>
            </tr>
            <tr>
                <td class="car-label">Model</td>
                <td class="car-colon">:</td>
                <td class="car-value">2023</td>
            </tr>
            <tr>
                <td class="car-label">Car No</td>
                <td class="car-colon">:</td>
                <td class="car-value">TN-05-IJ-3344</td>
            </tr>
        </table>
        <div class="card-footer">
            <button class="bg-warning">Edit</button>
            <button class="bg-danger">Delete</button>
        </div>
    </div>

    <!-- Car 6 -->
    <div class="car-card">
        <div class="toggle-switch">
            <label class="switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
            </label>
        </div>
        <div class="image-box">
            <img src="https://www.godigit.com/content/dam/godigit/directportal/en/tata-harrier.jpg" alt="Tata Harrier">
        </div>
        <table>
            <tr>
                <td class="car-label">Car Name</td>
                <td class="car-colon">:</td>
                <td class="car-value">Tata Harrier</td>
            </tr>
            <tr>
                <td class="car-label">Model</td>
                <td class="car-colon">:</td>
                <td class="car-value">2022</td>
            </tr>
            <tr>
                <td class="car-label">Car No</td>
                <td class="car-colon">:</td>
                <td class="car-value">TN-06-KL-5566</td>
            </tr>
        </table>
        <div class="card-footer">
            <button class="bg-warning">Edit</button>
            <button class="bg-danger">Delete</button>
        </div>
    </div>

</div>

@endsection
