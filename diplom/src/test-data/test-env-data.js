const envVariablesData = {
    hostUrl: process.env.HOST_URL,
    authEndPoint: process.env.AUTH_ENDPOINT,
    authCustom: process.env.AUTH_CUSTOM_LOGIN,
    authSecondCustom: process.env.AUTH_SECOND_CUSTOM_LOGIN,
    authAdminLogin: process.env.AUTH_ADMIN_LOGIN,
    authAdminPassword: process.env.AUTH_ADMIN_PASSWORD,
    serviceEmail: process.env.AUTOMATION_SERVICE_EMAIL,
    googleSheetToken: process.env.AUTOMATION_GOOGLE_TOKEN,
    servicePrivateKey: process.env.AUTOMATION_SERVICE_PRIVATE_KEY,
    telegramChatId: process.env.TELEGRAM_CHAT_ID,
    telegramBotToken: process.env.TELEGRAM_BOT_TOKEN,
    personOnDuty: process.env.PERSON_ON_DUTY,
    testEnv: process.env.TEST_ENV
};

module.exports = envVariablesData;
