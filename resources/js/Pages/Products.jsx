import React, { useState, useEffect } from 'react' // Mengimpor hook useState dan useEffect dari React
import { Head } from '@inertiajs/inertia-react' // Mengimpor komponen Head dari Inertia.js
import { Inertia } from '@inertiajs/inertia' // Mengimpor kelas Inertia dari Inertia.js
import { usePrevious } from 'react-use' // Mengimpor hook usePrevious dari react-use library
import { toast } from 'react-toastify' // Mengimpor komponen toast dari react-toastify library
import { useModalState } from '@/Hooks' // Mengimpor custom hook useModalState dari direktori '@/Hooks'
import { formatIDR } from '@/utils' // Mengimpor fungsi formatIDR dari direktori '@/utils'
import Authenticated from '@/Layouts/Authenticated' // Mengimpor komponen Authenticated dari direktori '@/Layouts/Authenticated'
import Pagination from '@/Components/Pagination' // Mengimpor komponen Pagination dari direktori '@/Components/Pagination'
import ModalConfirm from '@/Components/ModalConfirm' // Mengimpor komponen ModalConfirm dari direktori '@/Components/ModalConfirm'
import FormProductModal from '@/Modals/FormProductModal' // Mengimpor komponen FormProductModal dari direktori '@/Modals/FormProductModal'

export default function Products(props) {
    const { data: products, links } = props.products // Mendestrukturisasi properti products menjadi data dan links
    // data merupakan array produk, links merupakan objek tautan paginasi

    const [search, setSearch] = useState(props._search) // Menginisialisasi state search dengan nilai awal dari props._search
    const preValue = usePrevious(search) // Menggunakan hook usePrevious untuk mendapatkan nilai sebelumnya dari search

    const [product, setProduct] = useState(null) // Menginisialisasi state product dengan nilai awal null
    const formModal = useModalState(false) // Menggunakan custom hook useModalState dengan nilai awal false
    const toggle = (product = null) => {
        setProduct(product) // Mengatur state product sesuai dengan produk yang diberikan
        formModal.toggle() // Memanggil fungsi toggle dari custom hook useModalState untuk mengubah status tampilan modal
    }

    const confirmModal = useModalState(false) // Menggunakan custom hook useModalState dengan nilai awal false
    const handleDelete = (product) => {
        confirmModal.setData(product) // Mengatur data modal konfirmasi sesuai dengan produk yang diberikan
        confirmModal.toggle() // Memanggil fungsi toggle dari custom hook useModalState untuk mengubah status tampilan modal
    }

    const onDelete = () => {
        const product = confirmModal.data // Mengambil produk dari data modal konfirmasi
        if (product != null) {
            Inertia.delete(route('products.destroy', product), {
                onSuccess: () => toast.success('The Data has been deleted'), // Menampilkan notifikasi sukses menggunakan react-toastify saat data dihapus
            })
        }
    }

    useEffect(() => {
        if (preValue) {
            Inertia.get(
                route(route().current()),
                { q: search },
                {
                    replace: true,
                    preserveState: true,
                }
            )
        }
    }, [search])

    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight text-center">
                    Barang
                </h2>
            }
        >
             {/* // Menyetel judul halaman menggunakan komponen Head dari Inertia.js */}
            <Head title="Products" />
            <div className="py-12">
                <div className="flex flex-col w-full sm:px-6 lg:px-8 space-y-2">
                    <div className="card bg-white w-full">
                        <div className="card-body">
                            <div className="flex w-full mb-4 justify-between">
                                <div
                                    className="btn btn-neutral"
                                    onClick={() => toggle()}
                                >
                                    Tambah
                                </div>
                                <div className="form-control">
                                    <input
                                        type="text"
                                        className="input input-bordered"
                                        value={search}
                                        onChange={(e) =>
                                            setSearch(e.target.value)
                                        }
                                        placeholder="Search"
                                    />
                                </div>
                            </div>
                            <div className="overflow-x-auto">
                                <table className="table w-full table-zebra">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Harga</th>
                                            <th>Deskripsi</th>
                                            <th>Foto</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {products?.map((product) => (
                                            <tr key={product.id}>
                                                <td>{product.name}</td>
                                                <td>
                                                    {formatIDR(product.price)} 
                                                    {/* // Menggunakan fungsi formatIDR untuk memformat harga */}
                                                </td>
                                                <td>{product.description}</td>
                                                <td>
                                                    {product.photo_url !==
                                                        null && (
                                                        <img
                                                            width="100px"
                                                            src={
                                                                product.photo_url
                                                            }
                                                        />
                                                    )}
                                                </td>
                                                <td className="text-right">
                                                    <div
                                                        className="btn btn-primary mx-1"
                                                        onClick={() =>
                                                            toggle(product) // Memanggil fungsi toggle untuk membuka modal edit dengan produk yang dipilih
                                                        }
                                                    >
                                                        Edit
                                                    </div>
                                                    <div
                                                        className="btn btn-secondary mx-1"
                                                        onClick={() =>
                                                            handleDelete(
                                                                product
                                                            ) // Memanggil fungsi handleDelete untuk membuka modal konfirmasi hapus dengan produk yang dipilih
                                                        }
                                                    >
                                                        Delete
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                            {/* // Menggunakan komponen Pagination untuk menavigasi halaman produk */}
                            <Pagination links={links} params={{ q: search }} /> 
                        </div>
                    </div>
                </div>
            </div>
            <FormProductModal
                isOpen={formModal.isOpen}
                toggle={toggle}
                product={product}
            /> 
            {/* // Menampilkan modal form produk dengan props isOpen, toggle, dan product */}
            <ModalConfirm
                isOpen={confirmModal.isOpen}
                toggle={confirmModal.toggle}
                onConfirm={onDelete}
            /> 
            {/* // Menampilkan modal konfirmasi hapus dengan props isOpen, toggle, dan onConfirm */}
        </Authenticated>
    )
}
