const chaiHttp = require('chai-http');
const chai = require('chai');

chai.use(chaiHttp);

const envVariablesData = require('../test-data/test-env-data');

const {
    ROLE,
    MAX_TRIES,
    APPLICATION_JSON
} = require('../constants');


const authTokens = {};
let currentRole = null;

async function sendAuthPostRequest(requestBody) {
    const resp = await chai
        .request(envVariablesData.hostUrl)
        .post(envVariablesData.authEndPoint)
        .set('content-type', APPLICATION_JSON)
        .send(requestBody);

    return Promise.resolve(resp);
}

async function sendPostRequestWithToken(requestData, method, role = null) {
    let token = null;

    // If a role is provided, update the current role and generate a new token
    if (role === ROLE.ADMIN || role === ROLE.CUSTOM || role === ROLE.SECOND_CUSTOM) {
        currentRole = role; // Update current role
        await authenticateAndGenerateToken(role);
        token = authTokens[role];
    } else {
        // If no role is provided, use the token for the current role
        token = authTokens[currentRole];
    }

    if (!token) {
        console.warn('No token found for the current role or any other role.');
        return null;
    }

    let resp;

    async function makeRequest(request, requestBody, reqMethod, authTokenHeader) {
        console.log(`Making request with role: ${currentRole}`);
        return request[reqMethod](requestData.endPoint)
            .set(authTokenHeader)
            .send(requestBody);
    }

    async function attemptRequest(tries) {
        if (tries <= 0) {
            return null;
        }

        try {
            resp = await makeRequest(chai.request(envVariablesData.hostUrl), requestData.requestBody || requestData, method, token);

            if (resp?.body?.error?.message === 'Token is invalid.' || resp?.body?.error?.message === 'This action is unauthorized.') {
                console.log('Token is invalid. Trying to recreate the token....');
                await authenticateAndGenerateToken(currentRole);
                return attemptRequest(tries - 1);
            }
        } catch (error) {
            console.error('Error:', error);
        }

        return resp;
    }

    return attemptRequest(MAX_TRIES);
}

async function authenticateAndGenerateToken(role) {
    try {
        const loginRequestBody = {
            email: role === ROLE.CUSTOM
                ? envVariablesData.authCustom
                : role === ROLE.SECOND_CUSTOM
                    ? envVariablesData.authSecondCustom
                    : envVariablesData.authAdminLogin,
            password: envVariablesData.authAdminPassword
        };

        const loginResponse = await sendAuthPostRequest(loginRequestBody);
        authTokens[role] = { Authorization: `Bearer ${loginResponse?.body?.auth_token}` };
    } catch (error) {
        console.error('Error creating or updating token:', error);
    }
}

module.exports = {
    sendPostRequestWithToken
};
