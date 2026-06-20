<div class="container py-4">

    <h3 class="mb-4">Report Card Preferences</h3>

    <form id="prefsForm" method="POST" action="/<?= $appName ?>/card-preferences/save">

        <div class="card mb-3">

            <div class="card-header">
                School Identity
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">Printed Name</label>
                    <input class="form-control" name="printed_name"
                           value="<?= $prefs['printed_name'] ?? '' ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input class="form-control" name="address"
                           value="<?= $prefs['address'] ?? '' ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Telephone</label>
                    <input class="form-control" name="telephone"
                           value="<?= $prefs['telephone'] ?? '' ?>">
                </div>

            </div>
        </div>

        <div class="card mb-3">

            <div class="card-header">
                Theme & Branding
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4">
                        <label class="form-label">Theme</label>
                        <input class="form-control" name="theme"
                               value="<?= $prefs['theme'] ?? '' ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Primary Color</label>
                        <input type="color" class="form-control"
                               name="primary_color_accent"
                               value="<?= $prefs['primary_color_accent'] ?? '#000000' ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Secondary Color</label>
                        <input type="color" class="form-control"
                               name="secondary_color_accent"
                               value="<?= $prefs['secondary_color_accent'] ?? '#ffffff' ?>">
                    </div>

                </div>

            </div>
        </div>

        <div class="card mb-3">

            <div class="card-header">
                Logo Settings
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">Logo URL</label>
                    <input class="form-control" name="logo_url"
                           value="<?= $prefs['logo_url'] ?? '' ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Logo Position</label>
                    <select class="form-select" name="logo_position">
                        <option value="left">Left</option>
                        <option value="center">Center</option>
                        <option value="right">Right</option>
                    </select>
                </div>

                <div class="form-check">
                    <input type="hidden" name="logo_watermark" value="0">

                    <input class="form-check-input" type="checkbox"
                           name="logo_watermark" value="1"
                           <?= !empty($prefs['logo_watermark']) ? 'checked' : '' ?>>

                    <label class="form-check-label">
                        Use logo as watermark
                    </label>
                </div>

            </div>

        </div>

        <button type="button" id="savePrefs" class="btn btn-success">
            Save Preferences
        </button>

        <span id="status" class="ms-3"></span>

    </form>

</div>

<script>
document.getElementById('savePrefs')?.addEventListener('click', async function () {

    const form = document.getElementById('prefsForm');
    const status = document.getElementById('status');

    status.textContent = 'Saving...';

    try {

        const res = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form)
        });

        const data = await res.json();

        if (data.status === 'success') {

            status.textContent = 'Saved successfully';
            status.className = 'ms-3 text-success';

        } else {

            status.textContent = data.message || 'Failed';
            status.className = 'ms-3 text-danger';

        }

    } catch (e) {

        status.textContent = 'Network error';
        status.className = 'ms-3 text-danger';
    }

});
</script>








