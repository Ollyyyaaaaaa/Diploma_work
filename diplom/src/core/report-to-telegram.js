const fs = require('fs');
const TelegramBot = require('node-telegram-bot-api');

const envVariablesData = require('../test-data/test-env-data');

const {
    STATUS,
    MARKDOWN
} = require('../constants');

const resultJSONFile = fs.readFileSync('./src/mochaReports/mochawesome.json');
const skippedTestsJSONFile = fs.readFileSync('./src/test-data/skippedTests.json');

// general Variables
const results = JSON.parse(resultJSONFile);
const skippedTests = JSON.parse(skippedTestsJSONFile);

const testType = process.argv.slice(2).join(' ');
const tg = new TelegramBot(envVariablesData.telegramBotToken);

// function for sending message to telegram
async function postResultsToTelegram(message) {
    await tg.sendMessage(envVariablesData.telegramChatId, message, { parse_mode: MARKDOWN });
}

// function for forming a message with general results
async function generalResults(resultData) {
    const duration = Math.round(resultData.stats.duration / 1000);
    const skippedTestsData = await generateSkippedTestsTable(skippedTests);
    return `${(resultData.stats?.failures > 0) ? `‼️️ ${STATUS.ERRORS}\n` : ''}
*Test Environment:* ${envVariablesData.hostUrl}
*Tested Functionality:* ${testType}

*Passed:* ${resultData.stats?.passes}
*Failed:* ${resultData.stats?.failures}
*Skipped:* ${skippedTestsData?.skipped}

*Duration*: ${Math.floor(duration / 60)} min ${duration % 60} sec
${(skippedTestsData?.skipped > 0) ? `\n\`\`\`${skippedTestsData.table}\`\`\`` : ''}`;
}

// forming array of test results with errors and specific messages
async function getErrorsFromReport(testResults) {
    const finalResults = {
        testGroupName: '',
        testResults: []
    };

    for (const testGroup of testResults.results) {
        for (const singleTestResult of testGroup.suites[0].tests) {
            const testResult = {};
            testResult.name = singleTestResult.title;

            if (singleTestResult.fail === true) {
                testResult.fail = true;
                testResult.errs = singleTestResult.err?.message;
            } else {
                testResult.fail = false;
                testResult.errs = '';
            }

            finalResults.testResults.push(testResult);
        }

        finalResults.testGroupName = testGroup.title;
    }

    return finalResults;
}

// function for generating table with all skipped tests and issue IDs related to these tests
async function generateSkippedTestsTable(skippedTestsData) {
    let table = `${STATUS.SKIPPED}:\n`;
    let skippedTestsCount = 0;
    for (const testData of skippedTestsData) {
        skippedTestsCount += 1;
        table += `• ${testData.testName} | ISSUE ID ${testData.issueId}\n`;
    }

    return {
        table: table,
        skipped: skippedTestsCount
    };
}

// forming table with test data in specific format for telegram
async function pushErrorsToTable(res) {
    let table = `${STATUS.FAILED}:\n`;

    for (const testResult of res.testResults) {
        if (testResult.fail) {
            table += `• ${testResult.name} | ${testResult.errs}\n`;
        }
    }

    return table;
}

// function of forming a message with extended results to be sent to a specific channel
async function extendedResults(testResults) {
    const tableWithResults = await pushErrorsToTable(testResults);
    return `\n\`\`\`${tableWithResults}\`\`\``;
}

(async () => {
    let message = await generalResults(results);

    if (results.stats.failures > 0) {
        const errorsMessage = await extendedResults(await getErrorsFromReport(results));
        message += `\n${errorsMessage}`;
    }

    message += `\n\nPerson on duty: ${envVariablesData.personOnDuty}`;

    await postResultsToTelegram(message);
})();
