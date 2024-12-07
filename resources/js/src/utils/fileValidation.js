export const validateFileExtension = (file, allowedExtensions = ['html']) => {
    if (!file) return { valid: false, message: 'No file selected' };
    const fileName = file.name;
    const fileExtension = fileName.split('.').pop().toLowerCase();

    if (!allowedExtensions.includes(fileExtension)) {
        return { valid: false, message: `Only ${allowedExtensions.join(', ')} files are allowed.` };
    }

    return { valid: true, message: '' };
};
