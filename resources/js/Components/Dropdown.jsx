const DropDownContext = React.createContext(); // Membuat konteks untuk dropdown

const Dropdown = ({ children }) => {
    const [open, setOpen] = useState(false); // Membuat state 'open' dan 'setOpen' dengan nilai awal false

    const toggleOpen = () => {
        setOpen((previousState) => !previousState); // Mengubah nilai 'open' menjadi kebalikan dari nilai sebelumnya
    };

    return (
        <DropDownContext.Provider value={{ open, setOpen, toggleOpen }}>
            <div className="relative">{children}</div> // Menggunakan konteks untuk menyediakan nilai 'open', 'setOpen', dan 'toggleOpen' kepada komponen-komponen turunannya
        </DropDownContext.Provider>
    );
};

const Trigger = ({ children }) => {
    const { open, setOpen, toggleOpen } = useContext(DropDownContext); // Mengakses nilai 'open', 'setOpen', dan 'toggleOpen' dari konteks

    return (
        <>
            <div onClick={toggleOpen}>{children}</div> // Menggunakan fungsi 'toggleOpen' saat elemen ini diklik

            {open && <div className="fixed inset-0 z-40" onClick={() => setOpen(false)}></div>} // Menampilkan elemen overlay saat dropdown terbuka
        </>
    );
};

const Content = ({ align = 'right', width = '48', contentClasses = 'py-1 bg-white', children }) => {
    const { open, setOpen } = useContext(DropDownContext); // Mengakses nilai 'open' dan 'setOpen' dari konteks

    let alignmentClasses = 'origin-top';

    if (align === 'left') {
        alignmentClasses = 'origin-top-left left-0'; // Mengatur kelas CSS untuk mengatur posisi konten dropdown
    } else if (align === 'right') {
        alignmentClasses = 'origin-top-right right-0'; // Mengatur kelas CSS untuk mengatur posisi konten dropdown
    }

    let widthClasses = '';

    if (width === '48') {
        widthClasses = 'w-48'; // Mengatur kelas CSS untuk mengatur lebar konten dropdown
    }

    return (
        <>
            <Transition
                show={open}
                enter="transition ease-out duration-200"
                enterFrom="transform opacity-0 scale-95"
                enterTo="transform opacity-100 scale-100"
                leave="transition ease-in duration-75"
                leaveFrom="transform opacity-100 scale-100"
                leaveTo="transform opacity-0 scale-95"
            >
                {open && (
                    <div
                        className={`absolute z-50 mt-2 rounded-md shadow-lg ${alignmentClasses} ${widthClasses}`}
                        onClick={() => setOpen(false)} // Menutup dropdown saat di luar konten dropdown diklik
                    >
                        <div className={`rounded-md ring-1 ring-black ring-opacity-5 ` + contentClasses}>
                            {children} // Menampilkan konten dropdown
                        </div>
                    </div>
                )}
            </Transition>
        </>
    );
};

const DropdownLink = ({ href, method = 'post', as = 'a', children }) => {
    return (
        <Link
            href={href}
            method={method}
            as={as}
            className="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
        >
            {children} // Menampilkan tautan dalam dropdown
        </Link>
    );
};

Dropdown.Trigger = Trigger; // Menambahkan komponen Trigger sebagai properti pada objek Dropdown
Dropdown.Content = Content; // Menambahkan komponen Content sebagai properti pada objek Dropdown
Dropdown.Link = DropdownLink; // Menambahkan komponen DropdownLink sebagai properti pada objek Dropdown

export default Dropdown; // Ekspor default dari komponen Dropdown
