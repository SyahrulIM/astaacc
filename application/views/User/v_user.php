            <!-- Page content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <h1 class="mt-4">Pengguna</h1>
                    </div>
                </div>
                <!-- Button trigger modal Tambah Pengguna -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUser">
                    <i class="fa-solid fa-plus"></i> Tambah Pengguna
                </button>

                <!-- Flash messages -->
                <?php if ($this->session->flashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <?= $this->session->flashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <?= $this->session->flashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                <!-- End -->

                <!-- Modal Tambah Pengguna -->
                <div class="modal fade" id="addUser" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true">
                    <form method="post" action="<?php echo base_url('user/addUser') ?>" enctype="multipart/form-data">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="addUserLabel">Tambah Pengguna</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="inputNamaLengkap" class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="inputNamaLengkap" name="inputNamaLengkap" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="inputUsername" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="inputUsername" name="inputUsername" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="inputEmail">Email</label>
                                        <input type="email" class="form-control" id="inputEmail" name="inputEmail" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="inputPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="inputPassword" name="inputPassword" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="inputFoto">Foto</label>
                                        <input type="file" class="form-control" id="inputFoto" name="inputFoto" accept="image/*">
                                    </div>
                                    <div class="mb-3">
                                        <label for="inputRole" class="form-label">Role</label>
                                        <select class="form-select" id="inputRole" name="inputRole">
                                            <option selected disabled>Pilih Role</option>
                                            <?php foreach ($role as $rkey => $rvalue) { ?>
                                            <option value="<?php echo $rvalue->idrole; ?>"><?php echo $rvalue->nama_role; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- End -->

                <!-- Modal Edit Pengguna -->
                <div class="modal fade" id="editUser" tabindex="-1" aria-labelledby="editUserLabel" aria-hidden="true">
                    <form method="post" action="<?php echo base_url('user/editUser') ?>" enctype="multipart/form-data">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="editUserLabel">Edit Pengguna</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="editNamaLengkap" class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="editNamaLengkap" name="editNamaLengkap" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editUsername" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="editUsername" name="editUsername" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editEmail">Email</label>
                                        <input type="text" class="form-control" id="editEmail" name="editEmail" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editPassword" class="form-label">Password (kosongkan jika tidak ingin diubah)</label>
                                        <input type="password" class="form-control" id="editPassword" name="editPassword">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editFoto">Foto</label>
                                        <input type="file" class="form-control" id="editFoto" name="editFoto" accept="image/*">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editRole" class="form-label">Role</label>
                                        <select class="form-select" id="editRole" name="editRole">
                                            <?php foreach ($role as $rkey => $rvalue) { ?>
                                            <option value="<?php echo $rvalue->idrole; ?>"><?php echo $rvalue->nama_role; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- End -->
                <div class="row">
                    <div class="col">
                        <table id="tableproduct" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Lengkap</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Foto</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($user as $ukey => $uvalue) { ?>
                                <tr>
                                    <td><?php echo $ukey + 1; ?></td>
                                    <td><?php echo $uvalue->full_name; ?></td>
                                    <td><?php echo $uvalue->username; ?></td>
                                    <td><?php echo $uvalue->email; ?></td>
                                    <td><img src="<?php echo base_url('assets/image/user/' . $uvalue->foto); ?>" alt="<?php echo $uvalue->foto; ?>" width="100px" height="100px" style="border-radius: 50%; object-fit: cover;"></td>
                                    <td><?php echo $uvalue->nama_role; ?></td>
                                    <td>
                                        <?php if ($uvalue->iduser != 4) { ?>
                                        <button type="button" class="btn btn-warning btnEditUser" data-full_name="<?= $uvalue->full_name ?>" data-username="<?= $uvalue->username ?>" data-email="<?= $uvalue->email ?>" data-foto="<?= $uvalue->foto ?>" data-idrole="<?= $uvalue->idrole ?>" data-bs-toggle="modal" data-bs-target="#editUser">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <a href="<?php echo base_url('user/deleteUser?iduser=' . $uvalue->iduser); ?>">
                                            <button type="button" class="btn btn-danger">
                                                <i class="fa-solid fa-trash-can"></i> Hapus
                                            </button>
                                        </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
            </div>
            <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
            <!-- 2. DataTables JS -->
            <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
            <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
            <script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.js"></script>
            <script src="https://cdn.datatables.net/rowreorder/1.5.0/js/rowReorder.dataTables.js"></script>
            <script src="https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.js"></script>
            <script src="https://cdn.datatables.net/responsive/3.0.4/js/responsive.dataTables.js"></script>
            <!-- 3. Bootstrap bundle -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
            <!-- 4. Core theme JS -->
            <script src="<?php echo base_url(); ?>js/scripts.js"></script>

            <!-- Initialize DataTables AFTER all scripts are loaded -->
            <script>
                $(document).ready(function() {
                    new DataTable('#tableproduct', {
                        responsive: true,
                        layout: {
                            bottomEnd: {
                                paging: {
                                    firstLast: false
                                }
                            }
                        }
                    });
                });

                document.addEventListener("DOMContentLoaded", function() {
                    const form = document.querySelector('#addUser form');
                    const inputFoto = document.getElementById('inputFoto');

                    function isValidImage(file) {
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                        return allowedTypes.includes(file.type);
                    }

                    form.addEventListener('submit', function(e) {
                        if (inputFoto.files.length > 0 && !isValidImage(inputFoto.files[0])) {
                            alert('Format file Foto harus JPG, JPEG, atau PNG.');
                            e.preventDefault();
                            return;
                        }
                    });
                });

                document.addEventListener('DOMContentLoaded', function() {
                    const editButtons = document.querySelectorAll('.btnEditUser');

                    editButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            const full_name = this.getAttribute('data-full_name');
                            const username = this.getAttribute('data-username');
                            const email = this.getAttribute('data-email');
                            const foto = this.getAttribute('data-foto');
                            const idrole = this.getAttribute('data-idrole');

                            document.getElementById('editNamaLengkap').value = full_name;
                            document.getElementById('editUsername').value = username;
                            document.getElementById('editEmail').value = email;

                            // Reset value file karena keamanan browser tidak izinkan isi file input diset via JS
                            document.getElementById('editFoto').value = '';

                            // Set selected option pada select role
                            const selectRole = document.getElementById('editRole');
                            Array.from(selectRole.options).forEach(option => {
                                if (option.value === idrole) {
                                    option.selected = true;
                                } else {
                                    option.selected = false;
                                }
                            });
                        });
                    });
                });
            </script>
            </body>

            </html>