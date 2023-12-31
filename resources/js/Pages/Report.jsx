import React, { useState, useEffect } from 'react'
import DatePicker from 'react-datepicker'
import moment from 'moment'
import qs from 'qs'
import { Head } from '@inertiajs/inertia-react'
import { Inertia } from '@inertiajs/inertia'
import { usePrevious } from 'react-use'

import Authenticated from '@/Layouts/Authenticated'
import Pagination from '@/Components/Pagination'
import { formatIDR, formatDate } from '@/utils'

export default function Reports(props) {
    const { data: payrolls, links } = props.payrolls
    const { _startDate, _endDate } = props

    const [startDate, setStartDate] = useState(
        _startDate ? new Date(_startDate) : new Date()
    )
    const [endDate, setEndDate] = useState(
        _endDate ? new Date(_endDate) : new Date()
    )
    const preValue = usePrevious(`${startDate}-${endDate}`)

    const params = {
        startDate: moment(startDate).format('yyyy-MM-DD'),
        endDate: moment(endDate).format('yyyy-MM-DD'),
    }

    useEffect(() => {
        if (preValue) {
            Inertia.get(
                route(route().current()),
                params,
                {
                    replace: true,
                    preserveState: true,
                }
            )
        }
    }, [startDate, endDate])

    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight text-center">
                    Laporan
                </h2>
            }
        >
            <Head title="Payroll" />
            <div className="py-12">
                <div className="flex flex-col w-full sm:px-6 lg:px-8 space-y-2">
                    <div
                        className="card bg-white w-full"
                        style={{ minHeight: '400px' }}
                    >
                        <div className="card-body">
                            <div className="flex flex-col md:flex-row space-y-2 md:space-y-0 items-start md:items-stretch w-full mb-4 justify-between">
                                <div className="btn-group my-auto">
                                    <a
                                        className="btn btn-info btn-outline"
                                        href={`${route(
                                            'report.export'
                                        )}?${qs.stringify(params)}`}
                                    >
                                        Download Excel
                                    </a>
                                    <a
                                        className="btn btn-info btn-outline"
                                        href={`${route(
                                            'report.export-pdf'
                                        )}?${qs.stringify(params)}`}
                                    >
                                        Download PDF
                                    </a>
                                </div>
                                <div className="flex flex-row md:space-x-4">
                                    <div>
                                        <label className="label">
                                            <span className="label-text">
                                                Tanggal Awal
                                            </span>
                                        </label>
                                        <div className="relative">
                                            <DatePicker
                                                selected={startDate}
                                                onChange={(date) => {
                                                    setStartDate(date)
                                                }}
                                                format="dd/mm/yyyy"
                                                className="input input-bordered"
                                                nextMonthButtonLabel=">"
                                                previousMonthButtonLabel="<"
                                            />
                                            <div className="absolute right-2.5 rounded-l-none y-0 flex items-center top-2.5">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    className="h-6 w-6"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                        strokeWidth={2}
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                    />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label className="label">
                                            <span className="label-text">
                                                Tanggal Akhir
                                            </span>
                                        </label>
                                        <div className="relative">
                                            <DatePicker
                                                selected={endDate}
                                                onChange={(date) => {
                                                    setEndDate(date)
                                                }}
                                                format="dd/mm/yyyy"
                                                className="input input-bordered"
                                                nextMonthButtonLabel=">"
                                                previousMonthButtonLabel="<"
                                            />
                                            <div className="absolute right-2.5 rounded-l-none y-0 flex items-center top-2.5">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    className="h-6 w-6"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                        strokeWidth={2}
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                    />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="overflow-x-auto">
                                <table className="table w-full table-zebra">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nama Karyawan</th>
                                            <th>Kontak</th>
                                            <th>Total</th>
                                            <th>Jumlah Item</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {payrolls.map((payroll) => (
                                            <tr key={payroll.id}>
                                                <th>
                                                    {formatDate(payroll.date)}
                                                </th>
                                                <td>{payroll.employee.name}</td>
                                                <td>
                                                    {payroll.employee.whatsapp}
                                                </td>
                                                <td>
                                                    {formatIDR(payroll.recived)}
                                                </td>
                                                <td>
                                                    {formatIDR(
                                                        payroll.item_count
                                                    )}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                            <Pagination links={links} params={params} />
                        </div>
                    </div>
                </div>
            </div>
        </Authenticated>
    )
}
