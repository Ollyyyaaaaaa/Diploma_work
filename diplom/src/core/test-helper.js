const { expect } = require('chai');

// function for verification if there are no new JSON fields in actual response body
function verifyJSONFields(expectedBody, actualBody, path = []) {
    if (typeof expectedBody !== typeof actualBody) {
        throw new Error(`Type mismatch. Field: "${path.join('.')}", Expected type and value: ${typeof expectedBody} - ${expectedBody}, Actual type and value: ${typeof actualBody} - ${actualBody}.`);
    }

    if (Array.isArray(expectedBody) && Array.isArray(actualBody)) {
        if (expectedBody.length !== actualBody.length) {
            throw new Error(`Array length mismatch. Expected length: ${expectedBody.length}, Actual length: ${actualBody.length}.`);
        }

        for (let i = 0; i < expectedBody.length; i++) {
            verifyJSONFields(expectedBody[i], actualBody[i], [...path, i.toString()]);
        }
    } else if (typeof expectedBody === 'object' && typeof actualBody === 'object') {
        for (const field in actualBody) {
            if (Object.prototype.hasOwnProperty.call(actualBody, field)) {
                const currentPath = [...path, field];

                // check if the key is in the expected object
                if (!(field in expectedBody)) {
                    throw new Error(`New unexpected field in the Response Body: "${currentPath.join('.')}"`);
                }

                verifyJSONFields(expectedBody[field], actualBody[field], currentPath);
            }
        }

        // check for extra keys in the actual object
        for (const field in expectedBody) {
            if (Object.prototype.hasOwnProperty.call(expectedBody, field) && !(field in actualBody)) {
                const currentPath = [...path, field];
                throw new Error(`Missing expected field in the Response Body: "${currentPath.join('.')}"`);
            }
        }
    }
}

// function for verification that all expected fields are present in the response body, fields type are the same as in expected JSON
function validateJSONResponse(expectedResponse, actualResponse, currentFieldName = '') {
    const expectedFields = Object.keys(expectedResponse);

    for (const field of expectedFields) {
        const expectedValue = expectedResponse[field];
        const actualValue = actualResponse[field];

        if (expectedValue === null && actualValue === null) {
            return;
        }

        if (typeof expectedValue === 'object' && expectedValue !== null) {
            if (actualValue !== null && actualValue !== undefined) {
                validateJSONResponse(expectedValue, actualValue, `${currentFieldName}.${field}`);
            } else {
                throw new Error(`Field "${field}" is absent in Actual Response Body`);
            }
        } else {
            if (expectedValue !== null) {
                if (actualValue === undefined || actualValue === null) {
                    throw new Error(`Field "${field}" is absent in Actual Response Body`);
                }
            }
            expect(typeof expectedValue, `Field "${field}" has incorrect type in Response Body`).to.equal(typeof actualValue);
        }
    }
}

module.exports = {
    verifyJSONFields,
    validateJSONResponse
};
