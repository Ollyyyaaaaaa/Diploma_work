const fs = require('fs');
const { GoogleSpreadsheet } = require('google-spreadsheet');

const { isJson } = require('./modules');
const envVariablesData = require('../test-data/test-env-data');

const { TAB } = require('../constants');

const myArgs = process.argv.slice(2);
const PATH_TO_TEST_DATA_FOLDER = './src/test-data/';

// module for getting test data from a Google spreadsheet and storing to JSON file
(async () => {
    const doc = new GoogleSpreadsheet(envVariablesData.googleSheetToken);

    await doc.useServiceAccountAuth({
        client_email: envVariablesData.serviceEmail,
        private_key: envVariablesData.servicePrivateKey
    });
    await doc.loadInfo();

    const dataFromSheet = doc.sheetsByIndex[myArgs[0]];
    const rows = await dataFromSheet.getRows();
    const testDataJSON = [];
    const skippedTests = [];
    const columnNames = rows[0]._sheet.headerValues;
    const skipTag = 'skip';

    for (const el of rows) {
        if ((columnNames.includes('tag'))) {
            const testElementJSON = {};

            if (!el.tag?.includes(skipTag)) {
                for (const column of columnNames) {
                    if (el[column] !== '') {
                        testElementJSON[column] = isJson(el[column])
                            ? JSON.parse(el[column])
                            : el[column];
                    }
                }

                testDataJSON.push(testElementJSON);
            } else if (el.tag?.includes(skipTag)) {
                testElementJSON.testName = el.testName;
                testElementJSON.issueId = el.issueId;
                skippedTests.push(testElementJSON);
            }
        }
    }

    fs.writeFile(PATH_TO_TEST_DATA_FOLDER + myArgs[1], JSON.stringify(testDataJSON, null, TAB), (err) => {
        if (err) throw err;
    });
    fs.writeFile(`${PATH_TO_TEST_DATA_FOLDER}skippedTests.json`, JSON.stringify(skippedTests, null, TAB), (err) => {
        if (err) throw err;
    });
})();
