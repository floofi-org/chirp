if (location.hostname === 'localhost' || location.hostname === '127.0.0.1') {
    window.SERVER = "http://127.0.0.1:8080";
} else {
    window.SERVER = "https://voice-api.floo.fi";
}
