
# PHP API for p12Certificate Password Management

This PHP API allows users to change the password of `.p12` certificate files. The API accepts a POST request with the `.p12` certificate file and both current and new passwords, returning a new certificate file with the updated password.

## Features
- Password change for `.p12` certificates
- Temporary file storage with automatic cleanup of files older than 25 minutes

## Requirements
- PHP 7.0 or later
- OpenSSL extension enabled

## Usage

### Request Method
This API only accepts `POST` requests.

### Endpoint Parameters
- **action**: (string) Use `change-password` to initiate the password change process.
- **currentPassword**: (string) Current password of the `.p12` certificate file.
- **newPassword**: (string) New password to set for the `.p12` certificate file.
- **p12File**: (file) The `.p12` certificate file to be processed.

### Response

#### Success Response
- **message**: Success message with instructions about file deletion.
- **file**: Filename of the newly created `.p12` file with the updated password.

#### Error Responses
- **error**: Error message if the request method is invalid or parameters are missing.

### Example Request
To change the password of a `.p12` file, make a POST request with the following form data:

- `action=change-password`
- `currentPassword=<current_password>`
- `newPassword=<new_password>`
- `p12File=<path_to_p12_file>`

### Example Response
```json
{
  "message": "Password changed successfully. The certificate will be deleted in 25 minutes.",
  "file": "changed_abc123.p12"
}
```

### Error Examples
- Method not allowed: `{"error": "Invalid request method. Use POST."}`
- File upload error: `{"error": "File not uploaded."}`
- Incorrect password: `{"error": "Incorrect password or issue with the file."}`

## File Cleanup
Files in the `changed_certificates/` directory are automatically deleted 25 minutes after creation.

## Notes
Ensure that the directory `changed_certificates/` has write permissions.

## License
This project is licensed under the MIT License.
