function base64UrlToBase64(base64url) {
    let base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
    while (base64.length % 4) base64 += '=';
    return base64;
}

function base64ToBase64Url(base64) {
    return base64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
}

function arrayBufferToBase64(buffer) {
    let binary = '';
    const bytes = new Uint8Array(buffer);
    for (let i = 0; i < bytes.byteLength; i++) binary += String.fromCharCode(bytes[i]);
    return btoa(binary);
}

function base64ToArrayBuffer(base64) {
    const binary = atob(base64);
    const bytes = new Uint8Array(binary.length);
    for (let i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);
    return bytes.buffer;
}

function decodeCredentialResponse(credential) {
    return {
        id: credential.id,
        rawId: arrayBufferToBase64(credential.rawId),
        type: credential.type,
        response: {
            clientDataJSON: arrayBufferToBase64(credential.response.clientDataJSON),
            attestationObject: arrayBufferToBase64(credential.response.attestationObject),
        },
    };
}

function decodeAssertionResponse(credential) {
    return {
        id: credential.id,
        rawId: arrayBufferToBase64(credential.rawId),
        type: credential.type,
        response: {
            clientDataJSON: arrayBufferToBase64(credential.response.clientDataJSON),
            authenticatorData: arrayBufferToBase64(credential.response.authenticatorData),
            signature: arrayBufferToBase64(credential.response.signature),
            userHandle: credential.response.userHandle ? arrayBufferToBase64(credential.response.userHandle) : null,
        },
    };
}

function encodeServerOptions(options) {
    const publicKey = {
        challenge: base64ToArrayBuffer(options.challenge),
        rp: options.rp,
        user: {
            id: base64ToArrayBuffer(options.user.id),
            name: options.user.name,
            displayName: options.user.displayName,
        },
        pubKeyCredParams: options.pubKeyCredParams,
        timeout: options.timeout,
        attestation: options.attestation,
        authenticatorSelection: options.authenticatorSelection,
    };

    if (options.excludeCredentials && options.excludeCredentials.length > 0) {
        publicKey.excludeCredentials = options.excludeCredentials.map(cred => ({
            id: base64ToArrayBuffer(cred.id),
            type: cred.type,
        }));
    }

    return publicKey;
}

function encodeAssertionOptions(options) {
    const publicKey = {
        challenge: base64ToArrayBuffer(options.challenge),
        timeout: options.timeout,
    };

    if (options.allowCredentials && options.allowCredentials.length > 0) {
        publicKey.allowCredentials = options.allowCredentials.map(cred => ({
            id: base64ToArrayBuffer(cred.id),
            type: cred.type,
        }));
    }

    if (options.rpId) {
        publicKey.rpId = options.rpId;
    }

    return publicKey;
}

async function isWebAuthnSupported() {
    return window.PublicKeyCredential !== undefined;
}

function csrfHeaders() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': token ? token.content : '',
    };
}

async function registerBiometric(userId) {
    if (!(await isWebAuthnSupported())) {
        throw new Error('WebAuthn is not supported on this device.');
    }

    const response = await fetch('/biometric/register/options', {
        method: 'POST',
        headers: csrfHeaders(),
        body: JSON.stringify({ user_id: userId }),
    });

    if (!response.ok) {
        const text = await response.text();
        let err;
        try { err = JSON.parse(text); } catch(e) { err = { error: text.substring(0, 100) }; }
        throw new Error(err.error || 'Failed to get registration options');
    }

    const options = await response.json();
    const publicKey = encodeServerOptions(options);

    const credential = await navigator.credentials.create({ publicKey });
    const decoded = decodeCredentialResponse(credential);

    const verifyResponse = await fetch('/biometric/register', {
        method: 'POST',
        headers: csrfHeaders(),
        body: JSON.stringify({ user_id: userId, credential: decoded }),
    });

    if (!verifyResponse.ok) {
        const text = await verifyResponse.text();
        let err;
        try { err = JSON.parse(text); } catch(e) { err = { error: text.substring(0, 100) }; }
        throw new Error(err.error || 'Registration verification failed');
    }

    return await verifyResponse.json();
}

async function authenticateBiometric(email) {
    if (!(await isWebAuthnSupported())) {
        throw new Error('WebAuthn is not supported on this device.');
    }

    const response = await fetch('/biometric/authenticate/options', {
        method: 'POST',
        headers: csrfHeaders(),
        body: JSON.stringify({ email }),
    });

    if (!response.ok) {
        const text = await response.text();
        let err;
        try { err = JSON.parse(text); } catch(e) { err = { error: text.substring(0, 100) }; }
        throw new Error(err.error || 'No biometric credentials found');
    }

    const options = await response.json();
    const publicKey = encodeAssertionOptions(options);

    const credential = await navigator.credentials.get({ publicKey });
    const decoded = decodeAssertionResponse(credential);

    const verifyResponse = await fetch('/biometric/authenticate', {
        method: 'POST',
        headers: csrfHeaders(),
        body: JSON.stringify({ credential: decoded }),
    });

    if (!verifyResponse.ok) {
        const text = await verifyResponse.text();
        let err;
        try { err = JSON.parse(text); } catch(e) { err = { error: text.substring(0, 100) }; }
        throw new Error(err.error || 'Authentication failed');
    }

    return await verifyResponse.json();
}
