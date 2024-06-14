// function for checks if a key exists in an object or in any nested objects
function doesObjHasKey(obj, key) {
    let keyExistsInObj = Object.prototype.hasOwnProperty.call(obj, key);

    if (!keyExistsInObj) {
        for (const el in obj) {
            if (obj[el] !== null && typeof (obj[el]) === 'object') {
                keyExistsInObj = Object.prototype.hasOwnProperty.call(obj[el], key);

                if (keyExistsInObj) {
                    break;
                }
            }
        }
    }

    return keyExistsInObj;
}

// function for setting value in object for given key
function setObjKeyValue(obj, key, value) {
    if (!doesObjHasKey(obj, key)) {
        throw new Error(`Key "${key}" is absent in the object - impossible to set value for it.`);
    }

    if (Object.prototype.hasOwnProperty.call(obj, key)) {
        obj[key] = value;
    } else {
        for (const el in obj) {
            if (typeof (obj[el]) === 'object') {
                setObjKeyValue(obj[el], key, value);
            }
        }
    }
}

// function for check the object for the presence of the required parameter
function doesObjHasValue(obj, expValue) {
    if (!obj) {
        return false;
    }

    const doesValueExistInObjRoot = (obj, expValue) => {
        return Object.values(obj).includes(expValue);
    };

    return doesValueExistInObjRoot(obj, expValue) || (obj.data && doesValueExistInObjRoot(obj.data, expValue));
}

// function for updating value in object to newValue
function updateObjValue(obj, oldValue, newValue, hasBeenUpdated = false) {
    if (!doesObjHasValue(obj, oldValue)) {
        throw new Error(`Value "${oldValue}" is absent in the object - it is impossible to update it.`);
    }

    for (const [key, value] of Object.entries(obj)) {
        if (value === oldValue) {
            obj[key] = newValue;
            hasBeenUpdated = true;
        }
    }

    if (!hasBeenUpdated && obj.data) {
        updateObjValue(obj.data, oldValue, newValue, true);
    }
}

// сheck if the given item is a valid JSON object
function isJson(item) {
    item = typeof item !== 'string'
        ? JSON.stringify(item)
        : item;

    try {
        item = JSON.parse(item);
    } catch {
        return false;
    }

    return typeof item === 'object' && item !== null;
}

module.exports = {
    isJson,
    setObjKeyValue,
    updateObjValue,
    doesObjHasValue
};
