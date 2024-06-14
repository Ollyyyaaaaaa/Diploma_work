const fs = require('fs');
const { expect } = require('chai');

const modules = require('../core/modules');
const request = require('../core/request');
const testHelper = require('../core/test-helper');
const envVariablesData = require('../test-data/test-env-data');

const { PASSWORD } = require('../constants/index');

// General Variables
const testDataInJSON = fs.readFileSync('./src/test-data/testDataFromGoogle.json');
const allTestData = JSON.parse(testDataInJSON);

let response;
let savedFromResponse;

// Tests
describe('Api positive', () => {
    for (const testData of allTestData) {
        it(`${testData.testName}`, async () => {
            // update the body in the parameters of which there is a password
            if (modules.doesObjHasValue(testData.requestBody, PASSWORD)) {
                modules.updateObjValue(testData.requestBody, PASSWORD, envVariablesData.authAdminPassword);
            }

            // update the body if necessary, with the data saved from the previous test
            if (testData.modifyBody) {
                modules.setObjKeyValue(testData.requestBody, `${testData.modifyBody}`, `${savedFromResponse}`);
            }

            // update the endPoint if necessary, with the data saved from the previous test
            if (testData.modifyEndPoint) {
                testData.endPoint = testData.endPoint?.replace(`${testData.modifyEndPoint}`, `${savedFromResponse}`);
            }

            // header logs
            console.log('\x1b[1m\x1b[33mEndpoint:\x1b[0m', JSON.stringify(testData.endPoint));
            console.log('\x1b[1m\x1b[33mMethod:\x1b[0m', JSON.stringify(testData.method));
            console.log('\x1b[1m\x1b[33mRequest body:\x1b[0m', JSON.stringify(testData.requestBody));

            // send request and get response
            response = await request.sendPostRequestWithToken(testData, testData.method, testData.role);

            console.log('\x1b[1m\x1b[33mExpected Response body:\x1b[0m', JSON.stringify(testData.responseData));
            console.log('\x1b[1m\x1b[33mActual Response body:\x1b[0m', JSON.stringify(response.body));

            // verification of response status
            expect(response, `Response: ${response.text}`).to.have.status(testData.responseCode);

            // verification general parameters in response
            testHelper.validateJSONResponse(testData.responseData, response.body);
            testHelper.verifyJSONFields(testData.responseData, response.body);

            // save data for nex test
            if (testData.saveFromResponse) {
                const elements = response.body[testData.addObj];

                savedFromResponse = testData.addObj
                    ? elements[testData.saveFromResponse]
                    : response.body[testData.saveFromResponse];

                console.log('\x1b[1m\x1b[33maddSavedFromResponse:\x1b[0m', savedFromResponse);
            }
        });
    }
});
