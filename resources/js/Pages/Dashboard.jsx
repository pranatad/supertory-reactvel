import React, { useState, useEffect } from 'react'
import Authenticated from '@/Layouts/Authenticated'
import { Head } from '@inertiajs/inertia-react'

export default function Dashboard(props) {
    const {
        employee,
        product,
    } = props

    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight text-center">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12 flex flex-col items-center">
                <div className="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div className="py-4 px-4 text-center font-bold text-xl">
                        <div className="text-4xl">Selamat Datang di SuperTory, {props.auth.user.name}</div>   
                    </div>
                </div>
                <div className="h-20"></div> {/* Spacer */}
                <div className="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div className="py-4 px-4 text-center font-bold text-xl">
                        <div className="text-4xl">Detail</div>
                        SuperTory adalah sebuah platform manajemen inventaris yang dirancang untuk membantu Anda dalam melacak dan mengelola stok produk dengan efisien. Dengan SuperTory, Anda dapat mencatat pembelian dan penjualan, memonitor persediaan produk, dan menghasilkan laporan penjualan yang informatif. Platform ini juga menyediakan analisis grafik yang memungkinkan Anda untuk memahami tren penjualan dan mengambil keputusan berdasarkan data yang akurat. Dengan SuperTory, proses manajemen inventaris menjadi lebih mudah dan terorganisir. Selamat menggunakan SuperTory!
                    </div>
                </div>
                 <div className="h-20"></div> {/* Spacer */}
                <div className="grid grid-cols-2 gap-4">
  <div className="bg-white overflow-hidden shadow-sm rounded-lg text-center">
    <div className="py-4 px-4 font-bold text-xl">
      <div className="text-4xl">{product}</div>
      Total Barang
    </div>
  </div>
  <div className="bg-white overflow-hidden shadow-sm rounded-lg text-center">
    <div className="py-4 px-4 font-bold text-xl">
      <div className="text-4xl">{employee}</div>
      Total Karyawan
    </div>
  </div>
</div>
            </div>
        </Authenticated>
    )
}
