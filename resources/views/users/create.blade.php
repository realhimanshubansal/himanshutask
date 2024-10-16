<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Himanshu Task</title>
    <meta name="csrf_token" content="{{ csrf_token() }}" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        form div {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], 
        textarea, 
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="file"] {
            padding: 5px;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .error-message {
            color: red;
            font-size: 0.9em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        img {
            border-radius: 4px;
        }

    </style>
  
</head>
<body>
    <form id="userForm" enctype="multipart/form-data">
    @csrf
    <div>
        <label>Name:</label>
        <input type="text" name="name" id="name" >
        <span class="error-message" id="nameError"></span>
    </div>
    <div>
        <label>Email:</label>
        <input type="text" name="email" id="email" >
        <span class="error-message" id="emailError"></span>
    </div>
    <div>
        <label>Phone:</label>
        <input type="text" name="phone" id="phone" >
        <span class="error-message" id="phoneError"></span>
    </div>
    <div>
        <label>Description:</label>
        <textarea name="description" id="description"></textarea>
        <span class="error-message" id="descriptionError"></span>
    </div>
    <div>
        <label>Role:</label>
        <select name="role_id" id="role_id" >
            <option value="">Select Option</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->role_name }}</option>
            @endforeach
        </select>
        <span class="error-message" id="roleError"></span>
    </div>
    <div>
        <label>Profile Image:</label>
        <input type="file" name="profile_image" id="profile_image">
        <span class="error-message" id="profile_imageError"></span>
    </div>
    <button type="submit">Submit</button>
</form>

<table id="usersTable">
<thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Description</th>
            <th>Role</th>
            <th>Profile Image</th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($users[0]))
        @foreach($users as $user)
        <tr>
            <td>{{@$user->name}}</td>
            <td>{{@$user->email}}</td>
            <td>{{@$user->phone}}</td>
            <td>{{@$user->description ?? ''}}</td>
            <td>{{@$user->role->role_name}}</td>
            <td>
                <img src="{{ @$user->profile_image ? '/uploads/' . @$user->profile_image : asset('uploads/sampleimg.jpg') }}" width="50">
            </td>
        </tr>
        @endforeach
        @endif
        
    </tbody>
</table>

</body>
<script>
   document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let formValid = true;
    
    document.querySelectorAll('.error-message').forEach(span => span.textContent = '');

    let name = document.getElementById('name').value;
    let email = document.getElementById('email').value;
    let phone = document.getElementById('phone').value;
    let role_id = document.getElementById('role_id').value;
    
    if (name.trim() === '') {
        document.getElementById('nameError').textContent = 'Name is required.';
        formValid = false;
    }

    if (email.trim() === '') {
        document.getElementById('emailError').textContent = 'Email is required.';
        formValid = false;
    } else if (!/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(email)) {
        document.getElementById('emailError').textContent = 'Please provide a valid email address.';
        formValid = false;
    }

    if (phone.trim() === '') {
        document.getElementById('phoneError').textContent = 'Phone number is required.';
        formValid = false;
    } else if (!/^[6-9]\d{9}$/.test(phone)) {
        document.getElementById('phoneError').textContent = 'Please enter a valid 10-digit Indian phone number.';
        formValid = false;
    }

    if (role_id === '') {
        document.getElementById('roleError').textContent = 'Please select a role.';
        formValid = false;
    }

    if (formValid) {
        let formData = new FormData(this);

        fetch("{{ route('users.store') }}", {
            method: 'POST',
            body: formData,
            "_token": "{{ csrf_token() }}",
        })
        .then(response => response.json())
        .then(data => {
            console.log("data",data);
            
            if (data.errors) {
                for (let key in data.errors) {
                    document.getElementById(key + 'Error').textContent = data.errors[key][0];
                }
            } else {
                let user = data.user;
                let p_img = user.profile_image ? user.profile_image : 'sampleimg.jpg';
                let newRow = `
                    <tr>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.phone}</td>
                        <td>${user.description ? user.description  : ''}</td>
                        <td>${user.role.role_name}</td>
                        <td><img src="/uploads/${p_img}" width="50"></td>
                    </tr>
                `;
                document.querySelector('#usersTable tbody').insertAdjacentHTML('beforeend', newRow);
            }
        })
        .catch(error => console.error('Error:', error));
    }
});


</script>
</html>