require("./bootstrap");

Echo.private("testing-channel").listen("checkEvent", (e) => {
    console.log(e.catalog);
});
